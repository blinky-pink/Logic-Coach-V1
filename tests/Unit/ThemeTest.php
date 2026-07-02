<?php

namespace App\Tests\Unit;

use App\Entity\Theme;
use PHPUnit\Framework\TestCase;

class ThemeTest extends TestCase
{
    public function testThemeInitialization(): void
    {
        // Arrange
        $theme = new Theme();

        // Assert
        $this->assertNull($theme->getId());
        $this->assertNull($theme->getName());
        $this->assertNull($theme->getDescription());
        $this->assertNull($theme->getIllustration());
        $this->assertNull($theme->getPrimaryColor());
        $this->assertNull($theme->getCreatedAt());
        $this->assertNull($theme->getUpdatedAt());
        $this->assertCount(0, $theme->getUsers());
    }

    public function testName(): void
    {
        // Arrange
        $theme = new Theme();

        // Act
        $theme->setName('Zen');

        // Assert
        $this->assertSame('Zen', $theme->getName());
    }

    public function testDescription(): void
    {
        // Arrange
        $theme = new Theme();

        // Act
        $theme->setDescription('Thème orienté bien-être');

        // Assert
        $this->assertSame('Thème orienté bien-être', $theme->getDescription());
    }

    public function testIllustration(): void
    {
        // Arrange
        $theme = new Theme();

        // Act
        $theme->setIllustration('zen.png');

        // Assert
        $this->assertSame('zen.png', $theme->getIllustration());
    }

    public function testPrimaryColor(): void
    {
        // Arrange
        $theme = new Theme();

        // Act
        $theme->setPrimaryColor('#4CAF50');

        // Assert
        $this->assertSame('#4CAF50', $theme->getPrimaryColor());
    }

    public function testDates(): void
    {
        // Arrange
        $theme = new Theme();

        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        // Act
        $theme->setCreatedAt($createdAt);
        $theme->setUpdatedAt($updatedAt);

        // Assert
        $this->assertSame($createdAt, $theme->getCreatedAt());
        $this->assertSame($updatedAt, $theme->getUpdatedAt());
    }
}