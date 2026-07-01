<?php

namespace App\Controller;

use App\Entity\DailyEntry;
use App\Entity\User;
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

            /** @var User $user */
            $user = $this->getUser();

            $dailyEntry->setUser($user);
            $this->applyBusinessRules($dailyEntry);

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

            $this->applyBusinessRules($dailyEntry);

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

    private function applyBusinessRules(DailyEntry $dailyEntry): void
    {
        $sleepScore = $this->convertSleepHoursToScore($dailyEntry->getSleepHours());

        $score = $sleepScore
            + $dailyEntry->getEnergy()
            + $dailyEntry->getMotivation()
            + $dailyEntry->getMood()
            + (10 - $dailyEntry->getStress());

        $dailyEntry->setScore($score);

        if ($score >= 40) {
            $dailyEntry->setState('excellent');
            $dailyEntry->setMessage('Excellente journée en perspective.');
            $dailyEntry->setAdvice('Gardez ce rythme et prenez le temps de valoriser vos bonnes habitudes.');

            return;
        }

        if ($score >= 30) {
            $dailyEntry->setState('good');
            $dailyEntry->setMessage('Votre équilibre du jour est positif.');
            $dailyEntry->setAdvice('Continuez sur cette dynamique en conservant des pauses régulières.');

            return;
        }

        if ($score >= 20) {
            $dailyEntry->setState('average');
            $dailyEntry->setMessage('Votre journée semble correcte, avec quelques points à surveiller.');
            $dailyEntry->setAdvice('Choisissez une priorité simple et avancez étape par étape.');

            return;
        }

        if ($score >= 10) {
            $dailyEntry->setState('difficult');
            $dailyEntry->setMessage('Votre journée semble demander plus d’attention.');
            $dailyEntry->setAdvice('Allégez votre programme si possible et accordez-vous un vrai moment de repos.');

            return;
        }

        $dailyEntry->setState('critical');
        $dailyEntry->setMessage('Votre journée semble particulièrement difficile.');
        $dailyEntry->setAdvice('Priorisez votre récupération et demandez du soutien si vous en ressentez le besoin.');
    }

    private function convertSleepHoursToScore(float $sleepHours): int
    {
        if ($sleepHours < 4) {
            return 0;
        }

        if ($sleepHours < 5) {
            return 4;
        }

        if ($sleepHours < 6) {
            return 5;
        }

        if ($sleepHours < 7) {
            return 6;
        }

        if ($sleepHours < 8) {
            return 8;
        }

        if ($sleepHours <= 9) {
            return 10;
        }

        return 9;
    }
}
