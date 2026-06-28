<?php

namespace App\Controller;

use App\Entity\DailyEntry;
use App\Form\DailyEntryType;
use App\Repository\DailyEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/daily/entry')]
final class DailyEntryController extends AbstractController
{
    #[Route(name: 'app_daily_entry_index', methods: ['GET'])]
    public function index(DailyEntryRepository $dailyEntryRepository): Response
    {
        return $this->render('daily_entry/index.html.twig', [
            'daily_entries' => $dailyEntryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_daily_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dailyEntry = new DailyEntry();
        $form = $this->createForm(DailyEntryType::class, $dailyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dailyEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_daily_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('daily_entry/new.html.twig', [
            'daily_entry' => $dailyEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_entry_show', methods: ['GET'])]
    public function show(DailyEntry $dailyEntry): Response
    {
        return $this->render('daily_entry/show.html.twig', [
            'daily_entry' => $dailyEntry,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_daily_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DailyEntry $dailyEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DailyEntryType::class, $dailyEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_daily_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('daily_entry/edit.html.twig', [
            'daily_entry' => $dailyEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_daily_entry_delete', methods: ['POST'])]
    public function delete(Request $request, DailyEntry $dailyEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dailyEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dailyEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_daily_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
