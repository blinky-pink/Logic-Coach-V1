<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        $entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        foreach ($entityManager->getRepository(Message::class)->findAll() as $message) {
            $entityManager->remove($message);
        }

        foreach ($entityManager->getRepository(DailyEntry::class)->findAll() as $dailyEntry) {
            $entityManager->remove($dailyEntry);
        }

        $entityManager->flush();

        foreach ($entityManager->getRepository(User::class)->findAll() as $user) {
            $entityManager->remove($user);
        }

        $entityManager->flush();

        $user = new User();
        $user->setEmail('home-test@example.com');
        $user->setPassword('test-password');
        $user->setRoles(['ROLE_USER']);
        $user->setFirstname('Home');
        $user->setLastname('Test');
        $user->setPseudo('home-test');

        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        $client->request('GET', '/');

        self::assertResponseIsSuccessful();

        $entityManager->close();
    }
}