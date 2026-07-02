<?php

namespace App\Tests\Unit;

use App\Entity\DailyEntry;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class DailyEntryTest extends TestCase
{
    public function testDailyEntryInitialization(): void
    {
        // Arrange
        $dailyEntry = new DailyEntry();

        // Assert
        $this->assertNull($dailyEntry->getId());
        $this->assertNull($dailyEntry->getSleepHours());
        $this->assertNull($dailyEntry->getEnergy());
        $this->assertNull($dailyEntry->getStress());
        $this->assertNull($dailyEntry->getMotivation());
        $this->assertNull($dailyEntry->getMood());
        $this->assertNull($dailyEntry->getScore());
        $this->assertNull($dailyEntry->getState());
        $this->assertNull($dailyEntry->getMessage());
        $this->assertNull($dailyEntry->getAdvice());
        $this->assertNull($dailyEntry->getUser());
    }

    public function testSleepHours(): void
    {
        // Arrange
        $dailyEntry = new DailyEntry();

        // Act
        $dailyEntry->setSleepHours(7.5);

        // Assert
        $this->assertSame(7.5, $dailyEntry->getSleepHours());
    }

    public function testIndicators(): void
    {
        // Arrange
        $dailyEntry = new DailyEntry();

        // Act
        $dailyEntry->setEnergy(8);
        $dailyEntry->setStress(3);
        $dailyEntry->setMotivation(9);
        $dailyEntry->setMood(7);

        // Assert
        $this->assertSame(8, $dailyEntry->getEnergy());
        $this->assertSame(3, $dailyEntry->getStress());
        $this->assertSame(9, $dailyEntry->getMotivation());
        $this->assertSame(7, $dailyEntry->getMood());
    }

    public function testScoreStateMessageAdvice(): void
    {
        // Arrange
        $dailyEntry = new DailyEntry();

        // Act
        $dailyEntry->setScore(42);
        $dailyEntry->setState('excellent');
        $dailyEntry->setMessage('Très bonne journée.');
        $dailyEntry->setAdvice('Continuez ainsi.');

        // Assert
        $this->assertSame(42, $dailyEntry->getScore());
        $this->assertSame('excellent', $dailyEntry->getState());
        $this->assertSame('Très bonne journée.', $dailyEntry->getMessage());
        $this->assertSame('Continuez ainsi.', $dailyEntry->getAdvice());
    }

    public function testUserAssociation(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('user@test.fr');

        $dailyEntry = new DailyEntry();

        // Act
        $dailyEntry->setUser($user);

        // Assert
        $this->assertSame($user, $dailyEntry->getUser());
        $this->assertSame('user@test.fr', $dailyEntry->getUser()->getEmail());
    }

    public function testCompleteDailyEntry(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('user@test.fr');

        $dailyEntry = new DailyEntry();

        // Act
        $dailyEntry->setSleepHours(8);
        $dailyEntry->setEnergy(9);
        $dailyEntry->setStress(2);
        $dailyEntry->setMotivation(8);
        $dailyEntry->setMood(9);
        $dailyEntry->setScore(44);
        $dailyEntry->setState('excellent');
        $dailyEntry->setMessage('Très bonne journée.');
        $dailyEntry->setAdvice('Continuez vos bonnes habitudes.');
        $dailyEntry->setUser($user);

        // Assert
        $this->assertSame(8.0, $dailyEntry->getSleepHours());
        $this->assertSame(9, $dailyEntry->getEnergy());
        $this->assertSame(2, $dailyEntry->getStress());
        $this->assertSame(8, $dailyEntry->getMotivation());
        $this->assertSame(9, $dailyEntry->getMood());
        $this->assertSame(44, $dailyEntry->getScore());
        $this->assertSame('excellent', $dailyEntry->getState());
        $this->assertSame('Très bonne journée.', $dailyEntry->getMessage());
        $this->assertSame('Continuez vos bonnes habitudes.', $dailyEntry->getAdvice());
        $this->assertSame($user, $dailyEntry->getUser());
    }
}