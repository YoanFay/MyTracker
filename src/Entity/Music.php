<?php

namespace App\Entity;

use App\Repository\MusicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'music')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MusicArtist $musicArtist = null;

    #[ORM\ManyToMany(targetEntity: MusicTags::class, inversedBy: 'music')]
    private Collection $musicTags;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mbid = null;

    #[ORM\OneToMany(mappedBy: 'music', targetEntity: MusicListen::class)]
    private Collection $musicListens;

    public function __construct()
    {
        $this->musicTags = new ArrayCollection();
        $this->musicListens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMusicArtist(): ?MusicArtist
    {
        return $this->musicArtist;
    }

    public function setMusicArtist(?MusicArtist $musicArtist): static
    {
        $this->musicArtist = $musicArtist;

        return $this;
    }

    /**
     * @return Collection<int, MusicTags>
     */
    public function getMusicTags(): Collection
    {
        return $this->musicTags;
    }

    public function addMusicTag(MusicTags $musicTag): static
    {
        if (!$this->musicTags->contains($musicTag)) {
            $this->musicTags->add($musicTag);
        }

        return $this;
    }

    public function removeMusicTag(MusicTags $musicTag): static
    {
        $this->musicTags->removeElement($musicTag);

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): static
    {
        $this->mbid = $mbid;

        return $this;
    }

    /**
     * @return Collection<int, MusicListen>
     */
    public function getMusicListens(): Collection
    {
        return $this->musicListens;
    }

    public function addMusicListen(MusicListen $musicListen): static
    {
        if (!$this->musicListens->contains($musicListen)) {
            $this->musicListens->add($musicListen);
            $musicListen->setMusic($this);
        }

        return $this;
    }

    public function removeMusicListen(MusicListen $musicListen): static
    {
        if ($this->musicListens->removeElement($musicListen)) {
            // set the owning side to null (unless already changed)
            if ($musicListen->getMusic() === $this) {
                $musicListen->setMusic(null);
            }
        }

        return $this;
    }
}
