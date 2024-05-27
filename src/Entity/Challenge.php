<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'challenge', targetEntity: ChallengeRun::class)]
    private Collection $runs;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $short_description = null;

    #[ORM\Column]
    private bool $available = false;

    #[ORM\Column]
    private bool $auto_score = false;

    #[ORM\Column]
    private bool $leaderboard = false;

    #[ORM\Column]
    private ?bool $lower_score_better = null;

    #[ORM\Column(length: 255)]
    private ?string $score_label = null;

    public function __construct()
    {
        $this->runs = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ChallengeRun>
     */
    public function getRuns(): Collection
    {
        return $this->runs;
    }

    public function addRun(ChallengeRun $run): static
    {
        if (!$this->runs->contains($run)) {
            $this->runs->add($run);
            $run->setChallenge($this);
        }

        return $this;
    }

    public function removeRun(ChallengeRun $run): static
    {
        if ($this->runs->removeElement($run)) {
            // set the owning side to null (unless already changed)
            if ($run->getChallenge() === $this) {
                $run->setChallenge(null);
            }
        }

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    public function isAutoScore(): ?bool
    {
        return $this->autoScore;
    }

    public function setAutoScore(?bool $autoScore): static
    {
        $this->autoScore = $autoScore;

        return $this;
    }

    public function isLeaderboard(): ?bool
    {
        return $this->leaderboard;
    }

    public function setLeaderboard(bool $leaderboard): static
    {
        $this->leaderboard = $leaderboard;

        return $this;
    }

    public function isLowerScoreBetter(): ?bool
    {
        return $this->lower_score_better;
    }

    public function setLowerScoreBetter(bool $lower_score_better): static
    {
        $this->lower_score_better = $lower_score_better;

        return $this;
    }

    public function getScoreLabel(): ?string
    {
        return $this->score_label;
    }

    public function setScoreLabel(string $score_label): static
    {
        $this->score_label = $score_label;

        return $this;
    }
}
