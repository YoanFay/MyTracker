<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbTomes = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MangaType $type = null;

    #[ORM\ManyToMany(targetEntity: MangaGenre::class, inversedBy: 'mangas')]
    private Collection $genres;

    #[ORM\ManyToMany(targetEntity: MangaTheme::class, inversedBy: 'mangas')]
    private Collection $themes;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MangaAuthor $author = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MangaEditor $editor = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: true)]
    private ?MangaDesigner $designer = null;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: MangaTome::class)]
    private Collection $mangaTomes;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->mangaTomes = new ArrayCollection();
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

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getNbTomes(): ?int
    {
        return $this->nbTomes;
    }

    public function setNbTomes(?int $nbTomes): static
    {
        $this->nbTomes = $nbTomes;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getType(): ?MangaType
    {
        return $this->type;
    }

    public function setType(?MangaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, MangaGenre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(MangaGenre $genre): static
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(MangaGenre $genre): static
    {
        $this->genres->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection<int, MangaTheme>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(MangaTheme $theme): static
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
        }

        return $this;
    }

    public function removeTheme(MangaTheme $theme): static
    {
        $this->themes->removeElement($theme);

        return $this;
    }

    public function getAuthor(): ?MangaAuthor
    {
        return $this->author;
    }

    public function setAuthor(?MangaAuthor $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getEditor(): ?MangaEditor
    {
        return $this->editor;
    }

    public function setEditor(?MangaEditor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getDesigner(): ?MangaDesigner
    {
        return $this->designer;
    }

    public function setDesigner(?MangaDesigner $designer): static
    {
        $this->designer = $designer;

        return $this;
    }

    /**
     * @return Collection<int, MangaTome>
     */
    public function getMangaTomes(): Collection
    {
        return $this->mangaTomes;
    }

    public function addMangaTome(MangaTome $mangaTome): static
    {
        if (!$this->mangaTomes->contains($mangaTome)) {
            $this->mangaTomes->add($mangaTome);
            $mangaTome->setManga($this);
        }

        return $this;
    }

    public function removeMangaTome(MangaTome $mangaTome): static
    {
        if ($this->mangaTomes->removeElement($mangaTome)) {
            // set the owning side to null (unless already changed)
            if ($mangaTome->getManga() === $this) {
                $mangaTome->setManga(null);
            }
        }

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
