<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DailyEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        if (!$user->hasSeenWelcome()) {
            return $this->render('dashboard/welcome.html.twig');
        }

        $latestDailyEntry = $dailyEntryRepository
            ->findLatestForUser($user);

        return $this->render('dashboard/index.html.twig', [
            'latest_daily_entry' => $latestDailyEntry,
        ]);
    }

    #[Route('/dashboard/welcome/complete', name: 'app_dashboard_welcome_complete', methods: ['POST'])]
    public function completeWelcome(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $this->isCsrfTokenValid('welcome_complete', (string) $request->request->get('_token'))
            ?: throw $this->createAccessDeniedException('Token CSRF invalide.');

        /** @var User $user */
        $user = $this->getUser();

        $user->setHasSeenWelcome(true);

        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
