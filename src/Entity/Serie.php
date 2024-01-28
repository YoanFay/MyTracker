<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SerieRepository::class)
 */
class Serie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plexId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=EpisodeShow::class, mappedBy="serie")
     */
    private $episodeShows;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tvdbId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vfName = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $artwork = null;

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(?string $plexId): static
    {
        $this->plexId = $plexId;

        return $this;
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

    public function getTvdbId(): ?int
    {
        return $this->tvdbId;
    }

    public function setTvdbId(?int $tvdbId): static
    {
        $this->tvdbId = $tvdbId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isVfName(): ?bool
    {
        return $this->vfName;
    }

    public function setVfName(bool $vfName): static
    {
        $this->vfName = $vfName;

        return $this;
    }

    /**
     * @return Collection<int, EpisodeShow>
     */
    public function getEpisodeShows(): Collection
    {
        return $this->episodeShows;
    }

    public function addEpisodeShow(EpisodeShow $episodeShow): static
    {
        if (!$this->episodeShows->contains($episodeShow)) {
            $this->episodeShows->add($episodeShow);
            $episodeShow->setSerie($this);
        }

        return $this;
    }

    public function removeEpisodeShow(EpisodeShow $episodeShow): static
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getSerie() === $this) {
                $episodeShow->setSerie(null);
            }
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
}
