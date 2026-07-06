<?php

namespace App\Tests\Service;

use App\Entity\DailyEntry;
use App\Service\BusinessRulesService;
use PHPUnit\Framework\TestCase;

final class BusinessRulesServiceTest extends TestCase
{
    private BusinessRulesService $businessRulesService;

    protected function setUp(): void
    {
        $this->businessRulesService = new BusinessRulesService();
    }

    public function testScoreFortyProducesExcellentState(): void
    {
        $dailyEntry = $this->createDailyEntry(
            sleepHours: 8.0,
            energy: 8,
            stress: 6,
            motivation: 8,
            mood: 10
        );

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(40, $dailyEntry->getScore());
        self::assertSame('excellent', $dailyEntry->getState());
        self::assertSame(
            'Excellente journée en perspective.',
            $dailyEntry->getMessage()
        );
        self::assertSame(
            'Gardez ce rythme et prenez le temps de valoriser vos bonnes habitudes.',
            $dailyEntry->getAdvice()
        );
    }

    public function testScoreThirtyProducesGoodState(): void
    {
        $dailyEntry = $this->createDailyEntry(
            sleepHours: 8.0,
            energy: 5,
            stress: 10,
            motivation: 5,
            mood: 10
        );

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(30, $dailyEntry->getScore());
        self::assertSame('good', $dailyEntry->getState());
    }

    public function testScoreTwentyProducesAverageState(): void
    {
        $dailyEntry = $this->createDailyEntry(
            sleepHours: 4.0,
            energy: 4,
            stress: 8,
            motivation: 4,
            mood: 6
        );

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(20, $dailyEntry->getScore());
        self::assertSame('average', $dailyEntry->getState());
    }

    public function testScoreTenProducesDifficultState(): void
    {
        $dailyEntry = $this->createDailyEntry(
            sleepHours: 3.0,
            energy: 0,
            stress: 10,
            motivation: 0,
            mood: 10
        );

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(10, $dailyEntry->getScore());
        self::assertSame('difficult', $dailyEntry->getState());
    }

    public function testScoreBelowTenProducesCriticalState(): void
    {
        $dailyEntry = $this->createDailyEntry(
            sleepHours: 3.0,
            energy: 1,
            stress: 10,
            motivation: 1,
            mood: 1
        );

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(3, $dailyEntry->getScore());
        self::assertSame('critical', $dailyEntry->getState());
    }

    public function testSleepBelowFourHoursProducesZeroSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(3.5);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(0, $dailyEntry->getScore());
    }

    public function testFourHoursProducesFourSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(4.0);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(4, $dailyEntry->getScore());
    }

    public function testFiveHoursProducesFiveSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(5.0);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(5, $dailyEntry->getScore());
    }

    public function testSixHoursProducesSixSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(6.0);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(6, $dailyEntry->getScore());
    }

    public function testSevenHoursProducesEightSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(7.0);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(8, $dailyEntry->getScore());
    }

    public function testEightHoursProducesTenSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(8.0);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(10, $dailyEntry->getScore());
    }

    public function testMoreThanNineHoursProducesNineSleepScore(): void
    {
        $dailyEntry = $this->createSleepOnlyDailyEntry(9.5);

        $this->businessRulesService->apply($dailyEntry);

        self::assertSame(9, $dailyEntry->getScore());
    }

    private function createSleepOnlyDailyEntry(float $sleepHours): DailyEntry
    {
        return $this->createDailyEntry(
            sleepHours: $sleepHours,
            energy: 0,
            stress: 10,
            motivation: 0,
            mood: 0
        );
    }

    private function createDailyEntry(
        float $sleepHours,
        int $energy,
        int $stress,
        int $motivation,
        int $mood
    ): DailyEntry {
        $dailyEntry = new DailyEntry();
        $dailyEntry->setSleepHours($sleepHours);
        $dailyEntry->setEnergy($energy);
        $dailyEntry->setStress($stress);
        $dailyEntry->setMotivation($motivation);
        $dailyEntry->setMood($mood);

        return $dailyEntry;
    }
}