<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DailyEntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(
        DailyEntryRepository $dailyEntryRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $latestDailyEntry = $dailyEntryRepository
            ->findLatestForUser($user);

        return $this->render('dashboard/index.html.twig', [
            'latest_daily_entry' => $latestDailyEntry,
        ]);
    }
}