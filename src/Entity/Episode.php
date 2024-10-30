<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $plexId;

    #[ORM\ManyToOne(targetEntity: Serie::class, inversedBy: "episodes")]
    #[ORM\JoinColumn(nullable: false)]
    private $serie;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "episodes")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "integer", nullable: true)]
    private $tvdbId;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $saison;

    #[ORM\Column(type: "integer")]
    private $saisonNumber;

    #[ORM\Column(type: "integer")]
    private $episodeNumber;

    #[ORM\Column(type: "integer", nullable: true)]
    private $duration;

    #[ORM\Column(type: "boolean")]
    private $vfName = false;

    #[ORM\OneToMany(mappedBy: 'episode', targetEntity: EpisodeShow::class)]
    private Collection $episodeShows;

    public function __construct()
    {
        $this->name = "TBA";
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

    public function setPlexId(?string $plexId): self
    {
        $this->plexId = $plexId;

        return $this;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): self
    {
        $this->serie = $serie;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getSaison(): ?string
    {
        return $this->saison;
    }

    public function setSaison(?string $saison): self
    {
        $this->saison = $saison;

        return $this;
    }

    public function getSaisonNumber(): ?int
    {
        return $this->saisonNumber;
    }

    public function setSaisonNumber(int $saisonNumber): self
    {
        $this->saisonNumber = $saisonNumber;

        return $this;
    }

    public function getEpisodeNumber(): ?int
    {
        return $this->episodeNumber;
    }

    public function setEpisodeNumber(int $episodeNumber): self
    {
        $this->episodeNumber = $episodeNumber;

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

    public function isVfName(): ?bool
    {
        return $this->vfName;
    }

    public function setVfName(bool $vfName): self
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
            $episodeShow->setEpisode($this);
        }

        return $this;
    }

    public function removeEpisodeShow(EpisodeShow $episodeShow): static
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getEpisode() === $this) {
                $episodeShow->setEpisode(null);
            }
        }

        return $this;
    }
}
