<?php

namespace App\Entity;

use App\Repository\AutoScorerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutoScorerRepository::class)]
class AutoScorer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $api_key = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_check = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_result = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Submission $last_submission = null;

    #[ORM\Column(length: 100)]
    private ?string $status = null;

    #[ORM\Column(length: 20)]
    private ?string $last_ip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->api_key;
    }

    public function setApiKey(string $api_key): static
    {
        $this->api_key = $api_key;

        return $this;
    }

    public function getLastCheck(): ?\DateTimeInterface
    {
        return $this->last_check;
    }

    public function setLastCheck(?\DateTimeInterface $last_check): static
    {
        $this->last_check = $last_check;

        return $this;
    }

    public function getLastResult(): ?\DateTimeInterface
    {
        return $this->last_result;
    }

    public function setLastResult(?\DateTimeInterface $last_result): static
    {
        $this->last_result = $last_result;

        return $this;
    }

    public function getLastSubmission(): ?Submission
    {
        return $this->last_submission;
    }

    public function setLastSubmission(?Submission $last_submission): static
    {
        $this->last_submission = $last_submission;

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

    public function getLastIp(): ?string
    {
        return $this->last_ip;
    }

    public function setLastIp(string $last_ip): static
    {
        $this->last_ip = $last_ip;

        return $this;
    }
}
