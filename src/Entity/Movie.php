<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "datetime")]
    private $showDate;

    #[ORM\Column(type: "integer", nullable: true)]
    private $tmdbId;

    #[ORM\Column(type: "integer", nullable: true)]
    private $duration;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "movies")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $plexId;

    #[ORM\ManyToMany(targetEntity: MovieGenre::class, mappedBy: 'movies')]
    private Collection $movieGenres;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $artwork = null;

    #[ORM\Column]
    private ?bool $updated = false;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->movieGenres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShowDate(): ?\DateTimeInterface
    {
        return $this->showDate;
    }

    public function setShowDate(\DateTimeInterface $showDate): self
    {
        $this->showDate = $showDate;

        return $this;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): self
    {
        $this->tmdbId = $tmdbId;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(?string $plexId): self
    {
        $this->plexId = $plexId;

        return $this;
    }

    /**
     * @return Collection<int, MovieGenre>
     */
    public function getMovieGenres(): Collection
    {
        return $this->movieGenres;
    }

    public function addMovieGenre(MovieGenre $movieGenre): static
    {
        if (!$this->movieGenres->contains($movieGenre)) {
            $this->movieGenres->add($movieGenre);
            $movieGenre->addMovie($this);
        }

        return $this;
    }

    public function removeMovieGenre(MovieGenre $movieGenre): static
    {
        if ($this->movieGenres->removeElement($movieGenre)) {
            $movieGenre->removeMovie($this);
        }

        return $this;
    }

    public function getArtwork(): ?string
    {
        return $this->artwork;
    }

    public function setArtwork(?string $artwork): static
    {
        $this->artwork = $artwork;

        return $this;
    }

    public function isUpdated(): ?bool
    {
        return $this->updated;
    }

    public function setUpdated(bool $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
