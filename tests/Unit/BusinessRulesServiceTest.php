<?php

namespace App\Tests\Unit;

use App\Entity\DailyEntry;
use App\Service\BusinessRulesService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BusinessRulesServiceTest extends TestCase
{
    #[DataProvider('businessRulesProvider')]
    public function testApplyBusinessRules(
        float $sleepHours,
        int $energy,
        int $stress,
        int $motivation,
        int $mood,
        int $expectedScore,
        string $expectedState,
        string $expectedMessage,
        string $expectedAdvice
    ): void {
        // Arrange
        $service = new BusinessRulesService();

        $dailyEntry = new DailyEntry();
        $dailyEntry->setSleepHours($sleepHours);
        $dailyEntry->setEnergy($energy);
        $dailyEntry->setStress($stress);
        $dailyEntry->setMotivation($motivation);
        $dailyEntry->setMood($mood);

        // Act
        $service->apply($dailyEntry);

        // Assert
        $this->assertSame($expectedScore, $dailyEntry->getScore());
        $this->assertSame($expectedState, $dailyEntry->getState());
        $this->assertSame($expectedMessage, $dailyEntry->getMessage());
        $this->assertSame($expectedAdvice, $dailyEntry->getAdvice());
    }

    public static function businessRulesProvider(): array
    {
        return [

            'Excellent day' => [
                8,
                10,
                1,
                10,
                10,
                49,
                'excellent',
                'Excellente journée en perspective.',
                'Gardez ce rythme et prenez le temps de valoriser vos bonnes habitudes.',
            ],

            'Good day' => [
                7,
                7,
                4,
                7,
                7,
                35,
                'good',
                'Votre équilibre du jour est positif.',
                'Continuez sur cette dynamique en conservant des pauses régulières.',
            ],

            'Average day' => [
                6,
                5,
                6,
                5,
                5,
                25,
                'average',
                'Votre journée semble correcte, avec quelques points à surveiller.',
                'Choisissez une priorité simple et avancez étape par étape.',
            ],

            'Difficult day' => [
                5,
                3,
                8,
                3,
                3,
                16,
                'difficult',
                'Votre journée semble demander plus d’attention.',
                'Allégez votre programme si possible et accordez-vous un vrai moment de repos.',
            ],

            'Critical day' => [
                3,
                1,
                10,
                1,
                1,
                3,
                'critical',
                'Votre journée semble particulièrement difficile.',
                'Priorisez votre récupération et demandez du soutien si vous en ressentez le besoin.',
            ],
        ];
    }
}