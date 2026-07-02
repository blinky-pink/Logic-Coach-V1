<?php

namespace App\Tests\Unit;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserInitialization(): void
    {
        $user = new User();

        $this->assertNull($user->getId());
        $this->assertNull($user->getEmail());
        $this->assertNull($user->getPassword());

        $this->assertContains('ROLE_USER', $user->getRoles());

        $this->assertCount(0, $user->getSentMessages());
        $this->assertCount(0, $user->getReceivedMessages());
        $this->assertCount(0, $user->getDailyEntries());

        $this->assertNull($user->getTheme());
    }

    public function testEmail(): void
    {
        $user = new User();

        $user->setEmail('user@test.fr');

        $this->assertSame('user@test.fr', $user->getEmail());
        $this->assertSame('user@test.fr', $user->getUserIdentifier());
    }

    public function testPassword(): void
    {
        $user = new User();

        $user->setPassword('password123');

        $this->assertSame('password123', $user->getPassword());
    }

    public function testRoles(): void
    {
        $user = new User();

        $user->setRoles(['ROLE_ADMIN']);

        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
    }
}