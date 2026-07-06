<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DashboardControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        foreach ($this->entityManager->getRepository(Message::class)->findAll() as $message) {
            $this->entityManager->remove($message);
        }

        foreach ($this->entityManager->getRepository(DailyEntry::class)->findAll() as $dailyEntry) {
            $this->entityManager->remove($dailyEntry);
        }

        $this->entityManager->flush();

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $this->user = $this->createUser(
            'dashboard-test@example.com'
        );

        $this->client->loginUser($this->user);
    }

    public function testDashboardWithoutDailyEntryShowsEmptyState(): void
    {
        $crawler = $this->client->request(
            'GET',
            '/dashboard'
        );

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'Aucun Daily Pulse enregistré',
            $crawler->html()
        );

        self::assertStringContainsString(
            'Faire ma première saisie',
            $crawler->html()
        );
    }

    public function testDashboardShowsLatestDailyEntryForAuthenticatedUser(): void
    {
        $oldDailyEntry = $this->createDailyEntry(
            $this->user,
            'OLD_USER_MESSAGE'
        );

        $latestDailyEntry = $this->createDailyEntry(
            $this->user,
            'LATEST_USER_MESSAGE'
        );

        $crawler = $this->client->request(
            'GET',
            '/dashboard'
        );

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'LATEST_USER_MESSAGE',
            $crawler->html()
        );

        self::assertStringNotContainsString(
            'OLD_USER_MESSAGE',
            $crawler->html()
        );

        self::assertStringContainsString(
            (string) $latestDailyEntry->getScore(),
            $crawler->html()
        );

        self::assertStringContainsString(
            $latestDailyEntry->getAdvice(),
            $crawler->html()
        );

        self::assertNotSame(
            $oldDailyEntry->getId(),
            $latestDailyEntry->getId()
        );
    }

    public function testDashboardDoesNotShowAnotherUsersDailyEntry(): void
    {
        $otherUser = $this->createUser(
            'dashboard-other-user@example.com'
        );

        $this->createDailyEntry(
            $otherUser,
            'FOREIGN_DAILY_ENTRY_MESSAGE'
        );

        $crawler = $this->client->request(
            'GET',
            '/dashboard'
        );

        self::assertResponseIsSuccessful();

        self::assertStringNotContainsString(
            'FOREIGN_DAILY_ENTRY_MESSAGE',
            $crawler->html()
        );

        self::assertStringContainsString(
            'Aucun Daily Pulse enregistré',
            $crawler->html()
        );
    }

    private function createUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('test-password');
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createDailyEntry(
        User $user,
        string $message
    ): DailyEntry {
        $dailyEntry = new DailyEntry();
        $dailyEntry->setSleepHours(8.0);
        $dailyEntry->setEnergy(8);
        $dailyEntry->setStress(2);
        $dailyEntry->setMotivation(8);
        $dailyEntry->setMood(8);
        $dailyEntry->setScore(38);
        $dailyEntry->setState('good');
        $dailyEntry->setMessage($message);
        $dailyEntry->setAdvice('DASHBOARD_TEST_ADVICE');
        $dailyEntry->setUser($user);

        $this->entityManager->persist($dailyEntry);
        $this->entityManager->flush();

        return $dailyEntry;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}