<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $type;

    #[ORM\Column(type: "boolean")]
    private $vfName = false;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $artwork = null;

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

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->animeGenres = new ArrayCollection();
        $this->animeThemes = new ArrayCollection();
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
}
