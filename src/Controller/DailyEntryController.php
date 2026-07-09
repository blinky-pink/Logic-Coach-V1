<?php

namespace App\Controller;

use App\Entity\DailyEntry;
use App\Entity\User;
use App\Form\DailyEntryType;
use App\Repository\DailyEntryRepository;
use App\Service\BusinessRulesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/daily/entry')]
final class DailyEntryController extends AbstractController
{
    public function __construct(
        private readonly BusinessRulesService $businessRulesService
    ) {
    }

    #[Route(name: 'app_daily_entry_index', methods: ['GET'])]
    public function index(DailyEntryRepository $dailyEntryRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('daily_entry/index.html.twig', [
            'daily_entries' => $dailyEntryRepository->findBy(
                ['user' => $user],
                ['id' => 'DESC']
            ),
        ]);
    }

    #[Route('/new', name: 'app_daily_entry_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $dailyEntry = new DailyEntry();

        $form = $this->createForm(DailyEntryType::class, $dailyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $dailyEntry->setUser($user);

            $this->businessRulesService->apply($dailyEntry);

            $entityManager->persist($dailyEntry);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_daily_entry_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('daily_entry/new.html.twig', [
            'daily_entry' => $dailyEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_entry_show', methods: ['GET'])]
    public function show(DailyEntry $dailyEntry): Response
    {
        $this->denyAccessToDailyEntry($dailyEntry);

        return $this->render('daily_entry/show.html.twig', [
            'daily_entry' => $dailyEntry,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_entry_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        DailyEntry $dailyEntry,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid(
            'delete'.$dailyEntry->getId(),
            $request->getPayload()->getString('_token')
        )) {
            $entityManager->remove($dailyEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute(
            'app_daily_entry_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }

    private function denyAccessToDailyEntry(DailyEntry $dailyEntry): void
    {
        if ($dailyEntry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez accéder qu’à vos propres suivis.'
            );
        }
    }
}