<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ResultData = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateTimeOfResult = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Test $test = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResultData(): ?string
    {
        return $this->ResultData;
    }

    public function setResultData(string $ResultData): static
    {
        $this->ResultData = $ResultData;
        return $this;
    }

    public function getDateTimeOfResult(): ?\DateTimeInterface
    {
        return $this->DateTimeOfResult;
    }

    public function setDateTimeOfResult(\DateTimeInterface $DateTimeOfResult): static
    {
        $this->DateTimeOfResult = $DateTimeOfResult;
        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;
        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): static
    {
        $this->test = $test;
        return $this;
    }
}