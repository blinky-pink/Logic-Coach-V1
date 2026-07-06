<?php

namespace App\Tests\Controller;

use App\Entity\DailyEntry;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MessageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private MessageRepository $messageRepository;
    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::getContainer()
            ->get(EntityManagerInterface::class);

        $this->messageRepository = static::getContainer()
            ->get(MessageRepository::class);

        foreach ($this->messageRepository->findAll() as $message) {
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

        $this->user = $this->createUser('message-test@example.com');

        $this->client->loginUser($this->user);
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/message');

        self::assertResponseIsSuccessful();
    }

    public function testIndexShowsUserHistoryAndHidesForeignMessages(): void
    {
        $receivedSender = $this->createUser(
            'history-received-sender@example.com'
        );

        $sentReceiver = $this->createUser(
            'history-sent-receiver@example.com'
        );

        $foreignSender = $this->createUser(
            'history-foreign-sender@example.com'
        );

        $foreignReceiver = $this->createUser(
            'history-foreign-receiver@example.com'
        );

        $receivedMessage = $this->createMessage(
            $receivedSender,
            $this->user
        );
        $receivedMessage->setMessage('MESSAGE_RECEIVED_BY_USER');

        $sentMessage = $this->createMessage(
            $this->user,
            $sentReceiver
        );
        $sentMessage->setMessage('MESSAGE_SENT_BY_USER');

        $foreignMessage = $this->createMessage(
            $foreignSender,
            $foreignReceiver
        );
        $foreignMessage->setMessage('MESSAGE_BETWEEN_OTHER_USERS');

        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/message');

        self::assertResponseIsSuccessful();

        self::assertStringContainsString(
            'MESSAGE_RECEIVED_BY_USER',
            $crawler->html()
        );

        self::assertStringContainsString(
            'MESSAGE_SENT_BY_USER',
            $crawler->html()
        );

        self::assertStringNotContainsString(
            'MESSAGE_BETWEEN_OTHER_USERS',
            $crawler->html()
        );
    }

    public function testNew(): void
    {
        $receiver = $this->createUser('receiver@example.com');

        $this->client->request('GET', '/message/new');

        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'message[receiver]' => (string) $receiver->getId(),
            'message[message]' => 'Message fonctionnel de test',
        ]);

        self::assertResponseRedirects('/message');

        self::assertSame(1, $this->messageRepository->count([]));

        $message = $this->messageRepository->findOneBy([]);

        self::assertInstanceOf(Message::class, $message);

        self::assertSame(
            $this->user->getId(),
            $message->getSender()?->getId()
        );

        self::assertSame(
            $receiver->getId(),
            $message->getReceiver()?->getId()
        );

        self::assertSame(
            'Message fonctionnel de test',
            $message->getMessage()
        );

        self::assertInstanceOf(
            \DateTimeImmutable::class,
            $message->getCreatedAt()
        );
    }

    public function testUserCanShowOwnMessage(): void
    {
        $receiver = $this->createUser('show-receiver@example.com');

        $message = $this->createMessage(
            $this->user,
            $receiver
        );

        $this->client->request(
            'GET',
            '/message/'.$message->getId()
        );

        self::assertResponseIsSuccessful();
    }

    public function testUserCannotShowForeignMessage(): void
    {
        $sender = $this->createUser('foreign-sender@example.com');
        $receiver = $this->createUser('foreign-receiver@example.com');

        $message = $this->createMessage($sender, $receiver);

        $this->client->request(
            'GET',
            '/message/'.$message->getId()
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotEditForeignMessage(): void
    {
        $sender = $this->createUser('edit-sender@example.com');
        $receiver = $this->createUser('edit-receiver@example.com');

        $message = $this->createMessage($sender, $receiver);

        $this->client->request(
            'GET',
            '/message/'.$message->getId().'/edit'
        );

        self::assertResponseStatusCodeSame(403);
    }

    public function testUserCannotDeleteForeignMessage(): void
    {
        $sender = $this->createUser('delete-sender@example.com');
        $receiver = $this->createUser('delete-receiver@example.com');

        $message = $this->createMessage($sender, $receiver);

        $this->client->request(
            'POST',
            '/message/'.$message->getId(),
            [
                '_token' => 'invalid-token',
            ]
        );

        self::assertResponseStatusCodeSame(403);

        self::assertSame(
            1,
            $this->messageRepository->count([
                'id' => $message->getId(),
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

    private function createMessage(User $sender, User $receiver): Message
    {
        $message = new Message();
        $message->setMessage('Message de test');
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setSender($sender);
        $message->setReceiver($receiver);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}