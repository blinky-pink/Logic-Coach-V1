<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ThemeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    /** @var EntityRepository<Theme> */
    private EntityRepository $themeRepository;

    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->themeRepository = $this->entityManager
            ->getRepository(Theme::class);

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

        foreach ($this->themeRepository->findAll() as $theme) {
            $this->entityManager->remove($theme);
        }

        foreach ($this->entityManager->getRepository(User::class)->findAll() as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $this->user = new User();
        $this->user->setEmail('theme-test@example.com');
        $this->user->setPassword('test-password');
        $this->user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        $this->client->loginUser($this->user);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/theme');

        self::assertResponseIsSuccessful();
    }

    public function testNew(): void
    {
        $this->client->request('GET', '/theme/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'theme[name]' => 'Theme Test',
            'theme[description]' => 'Description du theme',
            'theme[illustration]' => 'theme-test.png',
            'theme[primaryColor]' => '#123456',
            'theme[createdAt]' => '2026-07-06T10:00',
            'theme[updatedAt]' => '2026-07-06T10:00',
        ]);

        self::assertResponseRedirects('/theme');

        self::assertSame(
            1,
            $this->themeRepository->count([])
        );

        $theme = $this->themeRepository->findOneBy([]);

        self::assertInstanceOf(Theme::class, $theme);
        self::assertSame('Theme Test', $theme->getName());
        self::assertSame('Description du theme', $theme->getDescription());
        self::assertSame('theme-test.png', $theme->getIllustration());
        self::assertSame('#123456', $theme->getPrimaryColor());

        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $theme->getCreatedAt()
        );

        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $theme->getUpdatedAt()
        );
    }

    public function testShow(): void
    {
        $theme = $this->createTheme('Theme Show');

        $this->client->request(
            'GET',
            '/theme/'.$theme->getId()
        );

        self::assertResponseIsSuccessful();
    }

    public function testEdit(): void
    {
        $theme = $this->createTheme('Theme Edit');

        $themeId = $theme->getId();

        $this->client->request(
            'GET',
            '/theme/'.$themeId.'/edit'
        );

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Update', [
            'theme[name]' => 'Theme Modifie',
            'theme[description]' => 'Nouvelle description',
            'theme[illustration]' => 'theme-modifie.png',
            'theme[primaryColor]' => '#654321',
            'theme[createdAt]' => '2026-07-06T10:00',
            'theme[updatedAt]' => '2026-07-06T12:00',
        ]);

        self::assertResponseRedirects('/theme');

        $this->entityManager->clear();

        $updatedTheme = $this->themeRepository->find($themeId);

        self::assertInstanceOf(Theme::class, $updatedTheme);
        self::assertSame('Theme Modifie', $updatedTheme->getName());
        self::assertSame('Nouvelle description', $updatedTheme->getDescription());
        self::assertSame('theme-modifie.png', $updatedTheme->getIllustration());
        self::assertSame('#654321', $updatedTheme->getPrimaryColor());

        self::assertSame(
            '2026-07-06 12:00',
            $updatedTheme->getUpdatedAt()?->format('Y-m-d H:i')
        );
    }

    public function testDelete(): void
    {
        $theme = $this->createTheme('Theme Delete');

        $crawler = $this->client->request(
            'GET',
            '/theme/'.$theme->getId()
        );

        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Delete')->form();

        $this->client->submit($form);

        self::assertResponseRedirects('/theme');

        self::assertSame(
            0,
            $this->themeRepository->count([])
        );
    }

    private function createTheme(string $name): Theme
    {
        $theme = new Theme();
        $theme->setName($name);
        $theme->setDescription('Description de test');
        $theme->setIllustration('theme-test.png');
        $theme->setPrimaryColor('#123456');
        $theme->setCreatedAt(
            new \DateTimeImmutable('2026-07-06 10:00:00')
        );
        $theme->setUpdatedAt(
            new \DateTimeImmutable('2026-07-06 10:00:00')
        );

        $this->entityManager->persist($theme);
        $this->entityManager->flush();

        return $theme;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}