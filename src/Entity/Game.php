<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: "datetime")]
    private DateTime $releaseDate;

    /** @var Collection<int, GameDeveloper> $developer */
    #[ORM\ManyToMany(targetEntity: GameDeveloper::class, inversedBy: 'games')]
    private Collection $developer;

    /** @var Collection<int, GamePublishers> $publishers */
    #[ORM\ManyToMany(targetEntity: GamePublishers::class, inversedBy: 'games')]
    private Collection $publishers;

    /** @var Collection<int, GameMode> $modes */
    #[ORM\ManyToMany(targetEntity: GameMode::class, inversedBy: 'games')]
    private Collection $modes;

    /** @var Collection<int, GamePlatform> $platforms */
    #[ORM\ManyToMany(targetEntity: GamePlatform::class, inversedBy: 'games')]
    private Collection $platforms;

    /** @var Collection<int, GameTheme> $themes */
    #[ORM\ManyToMany(targetEntity: GameTheme::class, inversedBy: 'games')]
    private Collection $themes;

    /** @var Collection<int, GameGenre> $genre */
    #[ORM\ManyToMany(targetEntity: GameGenre::class, inversedBy: 'games')]
    private Collection $genre;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: true)]
    private ?GameSerie $serie = null;

    /** @var Collection<int, GameTracker> $gameTrackers */
    #[ORM\OneToMany(mappedBy: 'game', targetEntity: GameTracker::class)]
    private Collection $gameTrackers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    #[ORM\Column]
    private ?int $igdbId = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratingCount = null;

    #[ORM\Column(nullable: true)]
    private ?float $aggregatedRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $aggregatedRatingCount = null;


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


    public function setId(int $id): self
    {

        $this->id = $id;

        return $this;
    }


    public function getName(): string
    {

        return $this->name;
    }


    public function setName(string $name): static
    {

        $this->name = $name;

        return $this;
    }


    public function getReleaseDate(): ?DateTime
    {

        return $this->releaseDate;
    }


    public function setReleaseDate(DateTime $releaseDate): static
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


    public function getRating(): ?float
    {

        return $this->rating;
    }


    public function setRating(?float $rating): static
    {

        $this->rating = $rating;

        return $this;
    }


    public function getRatingCount(): ?int
    {

        return $this->ratingCount;
    }


    public function setRatingCount(?int $ratingCount): static
    {

        $this->ratingCount = $ratingCount;

        return $this;
    }


    public function getAggregatedRating(): ?float
    {

        return $this->aggregatedRating;
    }


    public function setAggregatedRating(?float $aggregatedRating): static
    {

        $this->aggregatedRating = $aggregatedRating;

        return $this;
    }


    public function getAggregatedRatingCount(): ?int
    {

        return $this->aggregatedRatingCount;
    }


    public function setAggregatedRatingCount(?int $aggregatedRatingCount): static
    {

        $this->aggregatedRatingCount = $aggregatedRatingCount;

        return $this;
    }
}
