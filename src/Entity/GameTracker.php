<?php

namespace App\Entity;

use App\Repository\GameTrackerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameTrackerRepository::class)]
class GameTracker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'gameTrackers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(type: "datetime")]
    private $startDate = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $endDate = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $completeDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $endTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $completeTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate($startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate($endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCompleteDate()
    {
        return $this->completeDate;
    }

    public function setCompleteDate($completeDate): static
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


    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {

        $this->id = $id;
    }
}
