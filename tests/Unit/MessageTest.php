<?php

namespace App\Tests\Unit;

use App\Entity\Message;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testMessageInitialization(): void
    {
        // Arrange
        $message = new Message();

        // Assert
        $this->assertNull($message->getId());
        $this->assertNull($message->getMessage());
        $this->assertNull($message->getCreatedAt());
        $this->assertNull($message->getSender());
        $this->assertNull($message->getReceiver());
    }

    public function testMessageContent(): void
    {
        // Arrange
        $message = new Message();

        // Act
        $message->setMessage('Bonjour Logic Coach');

        // Assert
        $this->assertSame('Bonjour Logic Coach', $message->getMessage());
    }

    public function testCreatedAt(): void
    {
        // Arrange
        $message = new Message();
        $createdAt = new \DateTimeImmutable();

        // Act
        $message->setCreatedAt($createdAt);

        // Assert
        $this->assertSame($createdAt, $message->getCreatedAt());
    }

    public function testSender(): void
    {
        // Arrange
        $sender = new User();
        $sender->setEmail('sender@test.fr');

        $message = new Message();

        // Act
        $message->setSender($sender);

        // Assert
        $this->assertSame($sender, $message->getSender());
        $this->assertSame('sender@test.fr', $message->getSender()->getEmail());
    }

    public function testReceiver(): void
    {
        // Arrange
        $receiver = new User();
        $receiver->setEmail('receiver@test.fr');

        $message = new Message();

        // Act
        $message->setReceiver($receiver);

        // Assert
        $this->assertSame($receiver, $message->getReceiver());
        $this->assertSame('receiver@test.fr', $message->getReceiver()->getEmail());
    }

    public function testCompleteMessage(): void
    {
        // Arrange
        $sender = new User();
        $sender->setEmail('sender@test.fr');

        $receiver = new User();
        $receiver->setEmail('receiver@test.fr');

        $createdAt = new \DateTimeImmutable();

        $message = new Message();

        // Act
        $message->setMessage('Message de test');
        $message->setCreatedAt($createdAt);
        $message->setSender($sender);
        $message->setReceiver($receiver);

        // Assert
        $this->assertSame('Message de test', $message->getMessage());
        $this->assertSame($createdAt, $message->getCreatedAt());
        $this->assertSame($sender, $message->getSender());
        $this->assertSame($receiver, $message->getReceiver());
    }
}