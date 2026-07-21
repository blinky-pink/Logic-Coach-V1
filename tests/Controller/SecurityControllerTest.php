<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->passwordHasher = static::getContainer()
            ->get(UserPasswordHasherInterface::class);

        foreach ($this->entityManager->getRepository(Message::class)->findAll() as $message) {
            $this->entityManager->remove($message);
        }

        foreach ($this->entityManager->getRepository(DailyEntry::class)->findAll() as $dailyEntry) {
            $this->entityManager->remove($dailyEntry);
        }

        $this->entityManager->flush();

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $user->setTheme(null);
        }

        $this->entityManager->flush();

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();
    }

    public function testAnonymousUserCanAccessLoginPage(): void
    {
        $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();
    }

    public function testAuthenticatedUserIsRedirectedFromLoginToDashboard(): void
    {
        $user = $this->createUser(
            'authenticated-security-test@example.com',
            'test-password'
        );

        $this->client->loginUser($user);

        $this->client->request('GET', '/login');

        self::assertResponseRedirects('/dashboard');
    }

    public function testUserCanLoginWithValidCredentials(): void
    {
        $this->createUser(
            'valid-login@example.com',
            'correct-password'
        );

        $crawler = $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $form['_username'] = 'valid-login@example.com';
        $form['_password'] = 'correct-password';

        $this->client->submit($form);

        self::assertResponseRedirects();

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertNotNull(
            static::getContainer()
                ->get('security.token_storage')
                ->getToken()
                ?->getUser()
        );
    }

    public function testUserCannotLoginWithUnknownEmail(): void
    {
        $crawler = $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $form['_username'] = 'unknown-user@example.com';
        $form['_password'] = 'test-password';

        $this->client->submit($form);

        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertSelectorExists('.login-error-container');
    }

    public function testUserCannotLoginWithInvalidPassword(): void
    {
        $this->createUser(
            'invalid-login@example.com',
            'correct-password'
        );

        $crawler = $this->client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $form = $crawler->filter('form')->form();

        $form['_username'] = 'invalid-login@example.com';
        $form['_password'] = 'wrong-password';

        $this->client->submit($form);

        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        self::assertSelectorExists('.login-error-container');
    }

    public function testLogoutDisconnectsUser(): void
    {
        $user = $this->createUser(
            'logout-security-test@example.com',
            'test-password'
        );

        $this->client->loginUser($user);

        $this->client->request('GET', '/logout');

        self::assertResponseRedirects('/login');

        $this->client->followRedirect();

        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/dashboard');

        self::assertResponseRedirects(
            'http://localhost/login'
        );
    }

    private function createUser(
        string $email,
        string $plainPassword
    ): User {
        $user = new User();

        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $user->setFirstname('Test');
        $user->setLastname('Utilisateur');
        $user->setPseudo('security-'.uniqid());

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $plainPassword
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