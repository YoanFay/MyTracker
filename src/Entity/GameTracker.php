<?php

namespace App\Entity;

use App\Repository\GameTrackerRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameTrackerRepository::class)]
class GameTracker
{
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'gameTrackers')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column(type: "datetime")]
    private DateTime $startDate;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $endDate = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTime $completeDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $endTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $completeTime = null;


    public function getId(): ?int
    {

        return $this->id;
    }


    public function setId(int $id): self
    {

        $this->id = $id;

        return $this;
    }


    public function getGame(): ?Game
    {

        return $this->game;
    }


    public function setGame(Game $game): static
    {

        $this->game = $game;

        return $this;
    }


    public function getStartDate(): DateTime
    {

        return $this->startDate;
    }


    public function setStartDate(DateTime $startDate): static
    {

        $this->startDate = $startDate;

        return $this;
    }


    public function getEndDate(): ?DateTime
    {

        return $this->endDate;
    }


    public function setEndDate(?DateTime $endDate): static
    {

        $this->endDate = $endDate;

        return $this;
    }


    public function getCompleteDate(): ?DateTime
    {

        return $this->completeDate;
    }


    public function setCompleteDate(?DateTime $completeDate): static
    {

        $this->completeDate = $completeDate;

        return $this;
    }


    public function getEndTime(): ?int
    {

        return $this->endTime;
    }


    public function setEndTime(?int $endTime): static
    {

        $this->endTime = $endTime;

        return $this;
    }


    public function getCompleteTime(): ?int
    {

        return $this->completeTime;
    }


    public function setCompleteTime(?int $completeTime): static
    {

        $this->completeTime = $completeTime;

        return $this;
    }
}
