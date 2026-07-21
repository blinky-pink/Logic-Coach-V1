<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationControllerTest extends WebTestCase
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
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();
    }

    public function testAnonymousUserCanAccessRegisterPage(): void
    {
        $this->client->request('GET', '/register');

        self::assertResponseIsSuccessful();
    }

    public function testRegistrationCreatesUserWithHashedPassword(): void
    {
        $this->client->request('GET', '/register');

        $this->client->submitForm('Créer mon compte', [
            'registration_form[firstname]' => 'Nouveau',
            'registration_form[lastname]' => 'Membre',
            'registration_form[pseudo]' => 'nouveau-membre',
            'registration_form[email]' => 'nouveau-membre@example.com',
            'registration_form[password][first]' => 'motdepasse123',
            'registration_form[password][second]' => 'motdepasse123',
        ]);

        self::assertResponseRedirects('/login');

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => 'nouveau-membre@example.com',
            ]);

        self::assertInstanceOf(User::class, $user);

        self::assertSame('Nouveau', $user->getFirstname());
        self::assertSame('Membre', $user->getLastname());
        self::assertSame('nouveau-membre', $user->getPseudo());

        self::assertSame(['ROLE_USER'], $user->getRoles());

        self::assertNotSame(
            'motdepasse123',
            $user->getPassword()
        );

        self::assertTrue(
            $this->passwordHasher->isPasswordValid(
                $user,
                'motdepasse123'
            )
        );
    }

    public function testRegistrationWithExistingEmailIsRejected(): void
    {
        $this->createUser('deja-inscrit@example.com');

        $this->client->request('GET', '/register');

        $crawler = $this->client->submitForm('Créer mon compte', [
            'registration_form[firstname]' => 'Déjà',
            'registration_form[lastname]' => 'Inscrit',
            'registration_form[pseudo]' => 'nouveau-pseudo',
            'registration_form[email]' => 'deja-inscrit@example.com',
            'registration_form[password][first]' => 'motdepasse123',
            'registration_form[password][second]' => 'motdepasse123',
        ]);

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'Un compte existe déjà avec cet email.',
            $crawler->html()
        );

        self::assertSame(
            1,
            $this->entityManager->getRepository(User::class)->count([
                'email' => 'deja-inscrit@example.com',
            ])
        );
    }

    public function testRegistrationWithEmptyFieldsIsRejected(): void
    {
        $this->client->request('GET', '/register');

        $this->client->submitForm('Créer mon compte', [
            'registration_form[firstname]' => '',
            'registration_form[lastname]' => '',
            'registration_form[pseudo]' => '',
            'registration_form[email]' => '',
            'registration_form[password][first]' => '',
            'registration_form[password][second]' => '',
        ]);

        self::assertResponseIsSuccessful();

        self::assertSame(
            0,
            $this->entityManager->getRepository(User::class)->count([])
        );
    }

    public function testRegistrationWithShortPasswordIsRejected(): void
    {
        $this->client->request('GET', '/register');

        $crawler = $this->client->submitForm('Créer mon compte', [
            'registration_form[firstname]' => 'Mot',
            'registration_form[lastname]' => 'Court',
            'registration_form[pseudo]' => 'motdepasse-court',
            'registration_form[email]' => 'motdepasse-court@example.com',
            'registration_form[password][first]' => '123',
            'registration_form[password][second]' => '123',
        ]);

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'Le mot de passe doit contenir au moins 8 caractères.',
            $crawler->html()
        );

        self::assertSame(
            0,
            $this->entityManager->getRepository(User::class)->count([])
        );
    }

    private function createUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('test-password');
        $user->setRoles(['ROLE_USER']);
        $user->setFirstname('Test');
        $user->setLastname('Utilisateur');
        $user->setPseudo('registration-'.uniqid());

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