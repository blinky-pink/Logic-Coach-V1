<?php

namespace App\Service;

use App\Entity\DailyEntry;

class BusinessRulesService
{
    public function apply(DailyEntry $dailyEntry): void
    {
        $sleepScore = $this->convertSleepHoursToScore($dailyEntry->getSleepHours());

        $score = $sleepScore
            + $dailyEntry->getEnergy()
            + $dailyEntry->getMotivation()
            + $dailyEntry->getMood()
            + (10 - $dailyEntry->getStress());

        $dailyEntry->setScore($score);

        if ($score >= 40) {
            $dailyEntry->setState('excellent');
            $dailyEntry->setMessage('Excellente journée en perspective.');
            $dailyEntry->setAdvice('Gardez ce rythme et prenez le temps de valoriser vos bonnes habitudes.');

            return;
        }

        if ($score >= 30) {
            $dailyEntry->setState('good');
            $dailyEntry->setMessage('Votre équilibre du jour est positif.');
            $dailyEntry->setAdvice('Continuez sur cette dynamique en conservant des pauses régulières.');

            return;
        }

        if ($score >= 20) {
            $dailyEntry->setState('average');
            $dailyEntry->setMessage('Votre journée semble correcte, avec quelques points à surveiller.');
            $dailyEntry->setAdvice('Choisissez une priorité simple et avancez étape par étape.');

            return;
        }

        if ($score >= 10) {
            $dailyEntry->setState('difficult');
            $dailyEntry->setMessage('Votre journée semble demander plus d’attention.');
            $dailyEntry->setAdvice('Allégez votre programme si possible et accordez-vous un vrai moment de repos.');

            return;
        }

        $dailyEntry->setState('critical');
        $dailyEntry->setMessage('Votre journée semble particulièrement difficile.');
        $dailyEntry->setAdvice('Priorisez votre récupération et demandez du soutien si vous en ressentez le besoin.');
    }

    private function convertSleepHoursToScore(float $sleepHours): int
    {
        if ($sleepHours < 4) {
            return 0;
        }

        if ($sleepHours < 5) {
            return 4;
        }

        if ($sleepHours < 6) {
            return 5;
        }

        if ($sleepHours < 7) {
            return 6;
        }

        if ($sleepHours < 8) {
            return 8;
        }

        if ($sleepHours <= 9) {
            return 10;
        }

        return 9;
    }
}