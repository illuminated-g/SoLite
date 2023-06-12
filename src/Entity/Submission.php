<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
class Submission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    private ?User $participant = null;

    #[ORM\ManyToOne(inversedBy: 'submissions')]
    private ?ChallengeRun $run = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $submitted = null;

    #[ORM\Column]
    private ?float $score = null;

    #[ORM\Column]
    private ?bool $approved = null;

    #[ORM\ManyToOne]
    private ?User $approved_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $approved_on = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): static
    {
        $this->participant = $participant;

        return $this;
    }

    public function getRun(): ?ChallengeRun
    {
        return $this->run;
    }

    public function setRun(?ChallengeRun $run): static
    {
        $this->run = $run;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getSubmitted(): ?\DateTimeInterface
    {
        return $this->submitted;
    }

    public function setSubmitted(\DateTimeInterface $submitted): static
    {
        $this->submitted = $submitted;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): static
    {
        $this->approved = $approved;

        return $this;
    }

    public function getApprovedBy(): ?User
    {
        return $this->approved_by;
    }

    public function setApprovedBy(?User $approvedBy): static
    {
        $this->approvedBy = $approved_by;

        return $this;
    }

    public function getApprovedOn(): ?\DateTimeInterface
    {
        return $this->approved_on;
    }

    public function setApprovedOn(?\DateTimeInterface $approved_on): static
    {
        $this->approved_on = $approved_on;

        return $this;
    }
}
