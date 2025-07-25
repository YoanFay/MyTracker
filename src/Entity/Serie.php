<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $plexId;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    /** @var Collection<int, Episode> $episodes */
    #[ORM\OneToMany(mappedBy: "serie", targetEntity: Episode::class)]
    private Collection $episodes;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $tvdbId;

    #[ORM\Column(type: "boolean")]
    private bool $vfName = false;

    /** @var Collection<int, Genres> $genres */
    #[ORM\ManyToMany(targetEntity: Genres::class, mappedBy: 'serie')]
    private Collection $genres;

    /** @var Collection<int, Tags> $tags */
    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'series')]
    private Collection $tags;

    /** @var Collection<int, AnimeGenre> $animeGenres */
    #[ORM\ManyToMany(targetEntity: AnimeGenre::class, mappedBy: 'serie', cascade: ["persist"])]
    private Collection $animeGenres;

    /** @var Collection<int, AnimeTheme> $animeThemes */
    #[ORM\ManyToMany(targetEntity: AnimeTheme::class, mappedBy: 'serie', cascade: ["persist"])]
    private Collection $animeThemes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'series')]
    #[ORM\JoinColumn(nullable: false)]
    private SerieType $serieType;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $firstAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextAired = null;

    /** @var Collection<int, SerieUpdate> $serieUpdates */
    #[ORM\OneToMany(mappedBy: 'serie', targetEntity: SerieUpdate::class)]
    private Collection $serieUpdates;

    /** @var Collection<int, Company> $company */
    #[ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'series')]
    private Collection $company;

    #[ORM\OneToOne(inversedBy: 'serie', cascade: ['persist', 'remove'])]
    private ?Artwork $artwork = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastSeasonName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextAiredType = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;


    public function __construct()
    {

        $this->name = "TBA";
        $this->episodes = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->animeGenres = new ArrayCollection();
        $this->animeThemes = new ArrayCollection();
        $this->serieUpdates = new ArrayCollection();
        $this->company = new ArrayCollection();
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


    public function getPlexId(): ?string
    {

        return $this->plexId;
    }


    public function setPlexId(?string $plexId): static
    {

        $this->plexId = $plexId;

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


    public function getTvdbId(): ?int
    {

        return $this->tvdbId;
    }


    public function setTvdbId(?int $tvdbId): static
    {

        $this->tvdbId = $tvdbId;

        return $this;
    }


    public function isVfName(): bool
    {

        return $this->vfName;
    }


    public function setVfName(bool $vfName): static
    {

        $this->vfName = $vfName;

        return $this;
    }


    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {

        return $this->episodes;
    }


    public function addEpisode(Episode $episode): static
    {

        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setSerie($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Genres>
     */
    public function getGenres(): Collection
    {

        return $this->genres;
    }


    public function addGenre(Genres $genre): static
    {

        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
            $genre->addSerie($this);
        }

        return $this;
    }


    public function removeGenre(Genres $genre): static
    {

        if ($this->genres->removeElement($genre)) {
            $genre->removeSerie($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {

        return $this->tags;
    }


    public function addTag(Tags $tag): static
    {

        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addSeries($this);
        }

        return $this;
    }


    public function removeTag(Tags $tag): static
    {

        if ($this->tags->removeElement($tag)) {
            $tag->removeSeries($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, AnimeGenre>
     */
    public function getAnimeGenres(): Collection
    {

        return $this->animeGenres;
    }


    public function addAnimeGenre(AnimeGenre $animeGenre): static
    {

        if (!$this->animeGenres->contains($animeGenre)) {
            $this->animeGenres->add($animeGenre);
            $animeGenre->addSerie($this);
        }

        return $this;
    }


    public function removeAnimeGenre(AnimeGenre $animeGenre): static
    {

        if ($this->animeGenres->removeElement($animeGenre)) {
            $animeGenre->removeSerie($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, AnimeTheme>
     */
    public function getAnimeThemes(): Collection
    {

        return $this->animeThemes;
    }


    public function addAnimeTheme(AnimeTheme $animeTheme): static
    {

        if (!$this->animeThemes->contains($animeTheme)) {
            $this->animeThemes->add($animeTheme);
            $animeTheme->addSerie($this);
        }

        return $this;
    }


    public function removeAnimeTheme(AnimeTheme $animeTheme): static
    {

        if ($this->animeThemes->removeElement($animeTheme)) {
            $animeTheme->removeSerie($this);
        }

        return $this;
    }


    public function getSlug(): ?string
    {

        return $this->slug;
    }


    public function setSlug(?string $slug): static
    {

        $this->slug = $slug;

        return $this;
    }


    public function getSerieType(): SerieType
    {

        return $this->serieType;
    }


    public function setSerieType(SerieType $serieType): static
    {

        $this->serieType = $serieType;

        return $this;
    }


    public function getStatus(): ?string
    {

        return $this->status;
    }


    public function setStatus(?string $status): static
    {

        $this->status = $status;

        return $this;
    }


    public function getFirstAired(): ?\DateTimeInterface
    {

        return $this->firstAired;
    }


    public function setFirstAired(?\DateTimeInterface $firstAired): static
    {

        $this->firstAired = $firstAired;

        return $this;
    }


    public function getLastAired(): ?\DateTimeInterface
    {

        return $this->lastAired;
    }


    public function setLastAired(?\DateTimeInterface $lastAired): static
    {

        $this->lastAired = $lastAired;

        return $this;
    }


    public function getNextAired(): ?\DateTimeInterface
    {

        return $this->nextAired;
    }


    public function setNextAired(?\DateTimeInterface $nextAired): static
    {

        $this->nextAired = $nextAired;

        return $this;
    }


    /**
     * @return Collection<int, SerieUpdate>
     */
    public function getSerieUpdates(): Collection
    {

        return $this->serieUpdates;
    }


    public function addSerieUpdate(SerieUpdate $serieUpdate): static
    {

        if (!$this->serieUpdates->contains($serieUpdate)) {
            $this->serieUpdates->add($serieUpdate);
            $serieUpdate->setSerie($this);
        }

        return $this;
    }


    /**
     * @return Collection<int, Company>
     */
    public function getCompany(): Collection
    {

        return $this->company;
    }


    public function addCompany(Company $company): static
    {

        if (!$this->company->contains($company)) {
            $this->company->add($company);
        }

        return $this;
    }


    public function removeCompany(Company $company): static
    {

        $this->company->removeElement($company);

        return $this;
    }


    public function getArtwork(): ?Artwork
    {

        return $this->artwork;
    }


    public function setArtwork(?Artwork $artwork): static
    {

        $this->artwork = $artwork;

        return $this;
    }


    public function getNameEng(): ?string
    {

        return $this->nameEng;
    }


    public function setNameEng(?string $nameEng): static
    {

        $this->nameEng = $nameEng;

        return $this;
    }


    public function getLastSeasonName(): ?string
    {

        return $this->lastSeasonName;
    }


    public function setLastSeasonName(?string $lastSeasonName): static
    {

        $this->lastSeasonName = $lastSeasonName;

        return $this;
    }


    public function getNextAiredType(): ?string
    {

        return $this->nextAiredType;
    }


    public function setNextAiredType(?string $nextAiredType): static
    {

        $this->nextAiredType = $nextAiredType;

        return $this;
    }


    public function getScore(): ?int
    {

        return $this->score;
    }


    public function setScore(?int $score): static
    {

        $this->score = $score;

        return $this;
    }
}
