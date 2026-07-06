<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private User $authenticatedUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->userRepository = static::getContainer()
            ->get(UserRepository::class);

        $this->passwordHasher = static::getContainer()
            ->get(UserPasswordHasherInterface::class);

        foreach ($this->entityManager->getRepository(Message::class)->findAll() as $message) {
            $this->entityManager->remove($message);
        }

        foreach ($this->entityManager->getRepository(DailyEntry::class)->findAll() as $dailyEntry) {
            $this->entityManager->remove($dailyEntry);
        }

        $this->entityManager->flush();

        foreach ($this->userRepository->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $this->authenticatedUser = new User();
        $this->authenticatedUser->setEmail('user-controller-test@example.com');
        $this->authenticatedUser->setRoles(['ROLE_ADMIN']);
        $this->authenticatedUser->setPassword(
            $this->passwordHasher->hashPassword(
                $this->authenticatedUser,
                'test-password'
            )
        );

        $this->entityManager->persist($this->authenticatedUser);
        $this->entityManager->flush();

        $this->client->loginUser($this->authenticatedUser);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/user');

        self::assertResponseIsSuccessful();
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/user/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'user[email]' => 'new-user@example.com',
            'user[roles]' => ['ROLE_USER'],
            'user[password]' => 'new-password',
        ]);

        self::assertResponseRedirects('/user');

        $createdUser = $this->userRepository->findOneBy([
            'email' => 'new-user@example.com',
        ]);

        self::assertInstanceOf(User::class, $createdUser);
        self::assertContains('ROLE_USER', $createdUser->getRoles());

        self::assertTrue(
            $this->passwordHasher->isPasswordValid(
                $createdUser,
                'new-password'
            )
        );

        self::assertNotSame(
            'new-password',
            $createdUser->getPassword()
        );
    }

    public function testShow(): void
    {
        $user = $this->createUser('show-user@example.com');

        $this->client->request(
            'GET',
            '/user/'.$user->getId()
        );

        self::assertResponseIsSuccessful();
    }

    public function testEdit(): void
    {
        $user = $this->createUser('edit-user@example.com');

        $userId = $user->getId();

        $this->client->request(
            'GET',
            '/user/'.$userId.'/edit'
        );

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Update', [
            'user[email]' => 'edited-user@example.com',
            'user[roles]' => ['ROLE_ADMIN'],
            'user[password]' => 'modified-password',
        ]);

        self::assertResponseRedirects('/user');

        $this->entityManager->clear();

        $updatedUser = $this->userRepository->find($userId);

        self::assertInstanceOf(User::class, $updatedUser);
        self::assertSame('edited-user@example.com', $updatedUser->getEmail());
        self::assertContains('ROLE_ADMIN', $updatedUser->getRoles());

        self::assertTrue(
            $this->passwordHasher->isPasswordValid(
                $updatedUser,
                'modified-password'
            )
        );

        self::assertNotSame(
            'modified-password',
            $updatedUser->getPassword()
        );
    }

    public function testDelete(): void
    {
        $user = $this->createUser('delete-user@example.com');

        $crawler = $this->client->request(
            'GET',
            '/user/'.$user->getId()
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Delete')->form();

        $this->client->submit($form);

        self::assertResponseRedirects('/user');

        self::assertNull(
            $this->userRepository->find($user->getId())
        );
    }

    private function createUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                'test-password'
            )
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}