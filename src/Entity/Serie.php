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
     * @ORM\Column(type="string", length=255)
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

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
        $this->setTvdbId(null);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(string $plexId): self
    {
        $this->plexId = $plexId;

        return $this;
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

    /**
     * @return Collection<int, EpisodeShow>
     */
    public function getEpisodeShows(): Collection
    {
        return $this->episodeShows;
    }

    public function addEpisodeShow(EpisodeShow $episodeShow): self
    {
        if (!$this->episodeShows->contains($episodeShow)) {
            $this->episodeShows[] = $episodeShow;
            $episodeShow->setSerie($this);
        }

        return $this;
    }

    public function removeEpisodeShow(EpisodeShow $episodeShow): self
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getSerie() === $this) {
                $episodeShow->setSerie(null);
            }
        }

        return $this;
    }

    public function getTvdbId(): ?int
    {
        return $this->tvdbId;
    }

    public function setTvdbId(?int $tvdbId): self
    {
        $this->tvdbId = $tvdbId;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
