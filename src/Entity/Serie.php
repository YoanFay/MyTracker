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
    private $id;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $plexId;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: "serie")]
    private $episodeShows;

    #[ORM\Column(type: "integer", nullable: true)]
    private $tvdbId;

    #[ORM\Column(type: "boolean")]
    private $vfName = false;

    #[ORM\ManyToMany(targetEntity: Genres::class, mappedBy: 'serie')]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: Tags::class, mappedBy: 'series')]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: AnimeGenre::class, mappedBy: 'serie', cascade: ["persist"])]
    private Collection $animeGenres;

    #[ORM\ManyToMany(targetEntity: AnimeTheme::class, mappedBy: 'serie', cascade: ["persist"])]
    private Collection $animeThemes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'series')]
    private ?SerieType $serieType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $firstAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $nextAired = null;

    #[ORM\OneToMany(mappedBy: 'serie', targetEntity: SerieUpdate::class)]
    private Collection $serieUpdates;

    #[ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'series')]
    private Collection $company;

    #[ORM\OneToOne(inversedBy: 'serie', cascade: ['persist', 'remove'])]
    private ?Artwork $artwork = null;

    public function __construct()
    {
        $this->name = "TBA";
        $this->episodeShows = new ArrayCollection();
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

    public function isVfName(): ?bool
    {
        return $this->vfName;
    }

    public function setVfName(bool $vfName): static
    {
        $this->vfName = $vfName;

        return $this;
    }

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

    public function getSerieType(): ?SerieType
    {
        return $this->serieType;
    }

    public function setSerieType(?SerieType $serieType): static
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

    public function removeSerieUpdate(SerieUpdate $serieUpdate): static
    {
        if ($this->serieUpdates->removeElement($serieUpdate)) {
            // set the owning side to null (unless already changed)
            if ($serieUpdate->getSerie() === $this) {
                $serieUpdate->setSerie(null);
            }
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
}
