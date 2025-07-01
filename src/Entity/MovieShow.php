<?php

namespace App\Entity;

use App\Repository\MovieShowRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieShowRepository::class)]
class MovieShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'movieShows')]
    #[ORM\JoinColumn(nullable: false)]
    private Movie $movie;

    #[ORM\Column(type: "datetime")]
    private DateTimeInterface $showDate;


    public function getId(): int
    {

        return $this->id;
    }


    public function setId(int $id): self
    {

        $this->id = $id;

        return $this;
    }


    public function getMovie(): Movie
    {

        return $this->movie;
    }


    public function setMovie(Movie $movie): static
    {

        $this->movie = $movie;

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
}
