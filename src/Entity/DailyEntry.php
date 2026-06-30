<?php

namespace App\Entity;

use App\Repository\DailyEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DailyEntryRepository::class)]
class DailyEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $sleepHours = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $energy = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $stress = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $motivation = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $mood = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $score = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advice = null;

    #[ORM\ManyToOne(inversedBy: 'dailyEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSleepHours(): ?float
    {
        return $this->sleepHours;
    }

    public function setSleepHours(float $sleepHours): static
    {
        $this->sleepHours = $sleepHours;

        return $this;
    }

    public function getEnergy(): ?int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): static
    {
        $this->energy = $energy;

        return $this;
    }

    public function getStress(): ?int
    {
        return $this->stress;
    }

    public function setStress(int $stress): static
    {
        $this->stress = $stress;

        return $this;
    }

    public function getMotivation(): ?int
    {
        return $this->motivation;
    }

    public function setMotivation(int $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getMood(): ?int
    {
        return $this->mood;
    }

    public function setMood(int $mood): static
    {
        $this->mood = $mood;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getAdvice(): ?string
    {
        return $this->advice;
    }

    public function setAdvice(?string $advice): static
    {
        $this->advice = $advice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
