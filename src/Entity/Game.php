<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "datetime")]
    private $releaseDate = null;

    #[ORM\ManyToMany(targetEntity: GameDeveloper::class, inversedBy: 'games')]
    private Collection $developer;

    #[ORM\ManyToMany(targetEntity: GamePublishers::class, inversedBy: 'games')]
    private Collection $publishers;

    #[ORM\ManyToMany(targetEntity: GameMode::class, inversedBy: 'games')]
    private Collection $modes;

    #[ORM\ManyToMany(targetEntity: GamePlatform::class, inversedBy: 'games')]
    private Collection $platforms;

    #[ORM\ManyToMany(targetEntity: GameTheme::class, inversedBy: 'games')]
    private Collection $themes;

    #[ORM\ManyToMany(targetEntity: GameGenre::class, inversedBy: 'games')]
    private Collection $genre;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: true)]
    private ?GameSerie $serie = null;

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameTracker::class)]
    private Collection $gameTrackers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    #[ORM\Column]
    private ?int $igdbId = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->developer = new ArrayCollection();
        $this->publishers = new ArrayCollection();
        $this->modes = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->gameTrackers = new ArrayCollection();
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

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, GameDeveloper>
     */
    public function getDevelopers(): Collection
    {
        return $this->developer;
    }

    public function addDeveloper(GameDeveloper $developer): static
    {
        if (!$this->developer->contains($developer)) {
            $this->developer->add($developer);
        }

        return $this;
    }

    public function removeDeveloper(GameDeveloper $developer): static
    {
        $this->developer->removeElement($developer);

        return $this;
    }

    /**
     * @return Collection<int, GamePublishers>
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    public function addPublisher(GamePublishers $publisher): static
    {
        if (!$this->publishers->contains($publisher)) {
            $this->publishers->add($publisher);
        }

        return $this;
    }

    public function removePublisher(GamePublishers $publisher): static
    {
        $this->publishers->removeElement($publisher);

        return $this;
    }

    /**
     * @return Collection<int, GameMode>
     */
    public function getModes(): Collection
    {
        return $this->modes;
    }

    public function addMode(GameMode $mode): static
    {
        if (!$this->modes->contains($mode)) {
            $this->modes->add($mode);
        }

        return $this;
    }

    public function removeMode(GameMode $mode): static
    {
        $this->modes->removeElement($mode);

        return $this;
    }

    /**
     * @return Collection<int, GamePlatform>
     */
    public function getPlatforms(): Collection
    {
        return $this->platforms;
    }

    public function addPlatform(GamePlatform $platform): static
    {
        if (!$this->platforms->contains($platform)) {
            $this->platforms->add($platform);
        }

        return $this;
    }

    public function removePlatform(GamePlatform $platform): static
    {
        $this->platforms->removeElement($platform);

        return $this;
    }

    /**
     * @return Collection<int, GameTheme>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(GameTheme $theme): static
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
        }

        return $this;
    }

    public function removeTheme(GameTheme $theme): static
    {
        $this->themes->removeElement($theme);

        return $this;
    }

    /**
     * @return Collection<int, GameGenre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    public function addGenre(GameGenre $genre): static
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
        }

        return $this;
    }

    public function removeGenre(GameGenre $genre): static
    {
        $this->genre->removeElement($genre);

        return $this;
    }

    public function getSerie(): ?GameSerie
    {
        return $this->serie;
    }

    public function setSerie(?GameSerie $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * @return Collection<int, GameTracker>
     */
    public function getGameTrackers(): Collection
    {
        return $this->gameTrackers;
    }

    public function addGameTracker(GameTracker $gameTracker): static
    {
        if (!$this->gameTrackers->contains($gameTracker)) {
            $this->gameTrackers->add($gameTracker);
            $gameTracker->setGame($this);
        }

        return $this;
    }

    public function removeGameTracker(GameTracker $gameTracker): static
    {
        if ($this->gameTrackers->removeElement($gameTracker)) {
            // set the owning side to null (unless already changed)
            if ($gameTracker->getGame() === $this) {
                $gameTracker->setGame(null);
            }
        }

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }

    public function getIgdbId(): ?int
    {
        return $this->igdbId;
    }

    public function setIgdbId(int $igdbId): static
    {
        $this->igdbId = $igdbId;

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
