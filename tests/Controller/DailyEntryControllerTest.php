<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\DailyEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DailyEntryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private DailyEntryRepository $dailyEntryRepository;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->dailyEntryRepository = static::getContainer()
            ->get(DailyEntryRepository::class);

        foreach ($this->entityManager->getRepository(Message::class)->findAll() as $message) {
            $this->entityManager->remove($message);
        }

        foreach ($this->dailyEntryRepository->findAll() as $dailyEntry) {
            $this->entityManager->remove($dailyEntry);
        }

        $this->entityManager->flush();

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $this->user = new User();
        $this->user->setEmail('daily-entry-test@example.com');
        $this->user->setPassword('test-password');
        $this->user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/daily/entry');

        self::assertResponseIsSuccessful();
    }

    public function testIndexOnlyShowsAuthenticatedUsersDailyEntries(): void
    {
        $ownDailyEntry = $this->createDailyEntry($this->user);
        $ownDailyEntry->setMessage('DAILY_ENTRY_OWN_USER');

        $otherUser = $this->createUser('other-index@example.com');

        $otherDailyEntry = $this->createDailyEntry($otherUser);
        $otherDailyEntry->setMessage('DAILY_ENTRY_OTHER_USER');

        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/daily/entry');

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'DAILY_ENTRY_OWN_USER',
            $crawler->html()
        );

        self::assertStringNotContainsString(
            'DAILY_ENTRY_OTHER_USER',
            $crawler->html()
        );
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/daily/entry/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'daily_entry[sleepHours]' => '8',
            'daily_entry[energy]' => '8',
            'daily_entry[stress]' => '2',
            'daily_entry[motivation]' => '8',
            'daily_entry[mood]' => '8',
        ]);

        self::assertResponseRedirects('/daily/entry');

        self::assertSame(
            1,
            $this->dailyEntryRepository->count([])
        );

        $dailyEntry = $this->dailyEntryRepository->findOneBy([]);

        self::assertInstanceOf(DailyEntry::class, $dailyEntry);

        self::assertSame(
            $this->user->getId(),
            $dailyEntry->getUser()?->getId()
        );

        self::assertSame(8.0, $dailyEntry->getSleepHours());
        self::assertSame(8, $dailyEntry->getEnergy());
        self::assertSame(2, $dailyEntry->getStress());
        self::assertSame(8, $dailyEntry->getMotivation());
        self::assertSame(8, $dailyEntry->getMood());

        self::assertNotNull($dailyEntry->getScore());
        self::assertNotNull($dailyEntry->getState());
        self::assertNotNull($dailyEntry->getMessage());
        self::assertNotNull($dailyEntry->getAdvice());
    }

    public function testUserCanShowOwnDailyEntry(): void
    {
        $dailyEntry = $this->createDailyEntry($this->user);

        $this->client->request(
            'GET',
            '/daily/entry/'.$dailyEntry->getId()
        );

        self::assertResponseIsSuccessful();
    }

    public function testUserCannotShowAnotherUsersDailyEntry(): void
    {
        $otherUser = $this->createUser('other-show@example.com');
        $dailyEntry = $this->createDailyEntry($otherUser);

        $this->client->request(
            'GET',
            '/daily/entry/'.$dailyEntry->getId()
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotEditAnotherUsersDailyEntry(): void
    {
        $otherUser = $this->createUser('other-edit@example.com');
        $dailyEntry = $this->createDailyEntry($otherUser);

        $this->client->request(
            'GET',
            '/daily/entry/'.$dailyEntry->getId().'/edit'
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotDeleteAnotherUsersDailyEntry(): void
    {
        $otherUser = $this->createUser('other-delete@example.com');
        $dailyEntry = $this->createDailyEntry($otherUser);

        $this->client->request(
            'POST',
            '/daily/entry/'.$dailyEntry->getId(),
            [
                '_token' => 'invalid-token',
            ]
        );

        self::assertResponseStatusCodeSame(403);

        self::assertSame(
            1,
            $this->dailyEntryRepository->count([
                'id' => $dailyEntry->getId(),
            ])
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

    private function createDailyEntry(User $user): DailyEntry
    {
        $dailyEntry = new DailyEntry();
        $dailyEntry->setSleepHours(8.0);
        $dailyEntry->setEnergy(8);
        $dailyEntry->setStress(2);
        $dailyEntry->setMotivation(8);
        $dailyEntry->setMood(8);
        $dailyEntry->setScore(38);
        $dailyEntry->setState('good');
        $dailyEntry->setMessage('Message de test');
        $dailyEntry->setAdvice('Conseil de test');
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