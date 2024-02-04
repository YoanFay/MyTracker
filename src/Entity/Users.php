<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $plexName;

    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: "user")]
    private $episodeShows;

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    private $movies;

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlexName(): ?string
    {
        return $this->plexName;
    }

    public function setPlexName(string $plexName): self
    {
        $this->plexName = $plexName;

        return $this;
    }

    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: "user")]
    public function getEpisodeShows(): Collection
    {
        return $this->episodeShows;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: "user")]
    public function addEpisodeShow(EpisodeShow $episodeShow): self
    {
        if (!$this->episodeShows->contains($episodeShow)) {
            $this->episodeShows[] = $episodeShow;
            $episodeShow->setUser($this);
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: "user")]
    public function removeEpisodeShow(EpisodeShow $episodeShow): self
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getUser() === $this) {
                $episodeShow->setUser(null);
            }
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->setUser($this);
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getUser() === $this) {
                $movie->setUser(null);
            }
        }

        return $this;
    }
}
