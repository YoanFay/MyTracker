<?php

namespace App\Entity;

use App\Repository\EpisodeShowRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EpisodeShowRepository::class)
 */
class EpisodeShow
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
     * @ORM\Column(type="datetime")
     */
    private $showDate;

    /**
     * @ORM\ManyToOne(targetEntity=Serie::class, inversedBy="episodeShows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $serie;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="episodeShows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tvdbId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $saison;

    /**
     * @ORM\Column(type="integer")
     */
    private $saisonNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $episodeNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vfName = false;

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

    public function getShowDate(): ?\DateTimeInterface
    {
        return $this->showDate;
    }

    public function setShowDate(\DateTimeInterface $showDate): self
    {
        $this->showDate = $showDate;

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

    public function setSaison(string $saison): self
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
}
