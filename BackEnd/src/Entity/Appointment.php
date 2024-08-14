<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateTimeOfAppointment = null;

    #[ORM\Column]
    private ?int $TypeOfTest = null;

    #[ORM\Column]
    private ?int $Confirmation = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $doctor = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTimeOfAppointment(): ?\DateTimeInterface
    {
        return $this->DateTimeOfAppointment;
    }

    public function setDateTimeOfAppointment(\DateTimeInterface $DateTimeOfAppointment): static
    {
        $this->DateTimeOfAppointment = $DateTimeOfAppointment;
        return $this;
    }

    public function getTypeOfTest(): ?int
    {
        return $this->TypeOfTest;
    }

    public function setTypeOfTest(int $TypeOfTest): static
    {
        $this->TypeOfTest = $TypeOfTest;
        return $this;
    }

    public function getConfirmation(): ?int
    {
        return $this->Confirmation;
    }

    public function setConfirmation(int $Confirmation): static
    {
        $this->Confirmation = $Confirmation;
        return $this;
    }

    public function getDoctor(): ?User
    {
        return $this->doctor;
    }

    public function setDoctor(?User $doctor): static
    {
        $this->doctor = $doctor;
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
}