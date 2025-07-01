<?php

namespace App\Entity;

use App\Repository\EpisodeShowRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeShowRepository::class)]
class EpisodeShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: "datetime")]
    private DateTimeInterface $showDate;

    #[ORM\ManyToOne(inversedBy: 'episodeShows')]
    #[ORM\JoinColumn(nullable: false)]
    private Episode $episode;


    public function getId(): int
    {

        return $this->id;
    }


    public function setId(int $id): self
    {

        $this->id = $id;

        return $this;

    }


    public function getShowDate(): DateTimeInterface
    {

        return $this->showDate;
    }


    public function setShowDate(DateTimeInterface $showDate): static
    {

        $this->showDate = $showDate;

        return $this;
    }


    public function getEpisode(): Episode
    {

        return $this->episode;
    }


    public function setEpisode(Episode $episode): static
    {

        $this->episode = $episode;

        return $this;
    }
}
