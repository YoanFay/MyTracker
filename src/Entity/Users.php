<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users
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
    private $plexName;

    /**
     * @ORM\OneToMany(targetEntity=EpisodeShow::class, mappedBy="user")
     */
    private $episodeShows;

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
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
            $episodeShow->setUser($this);
        }

        return $this;
    }

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
}
