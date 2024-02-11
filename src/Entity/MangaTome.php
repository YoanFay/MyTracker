<?php

namespace App\Entity;

use App\Repository\MangaTomeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaTomeRepository::class)]
class MangaTome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $tomeNumber = null;

    #[ORM\Column]
    private ?int $page = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\ManyToOne(inversedBy: 'mangaTomes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manga $manga = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $readingStartDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $readingEndDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTomeNumber(): ?int
    {
        return $this->tomeNumber;
    }

    public function setTomeNumber(int $tomeNumber): static
    {
        $this->tomeNumber = $tomeNumber;

        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        $this->page = $page;

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

    public function getManga(): ?Manga
    {
        return $this->manga;
    }

    public function setManga(?Manga $manga): static
    {
        $this->manga = $manga;

        return $this;
    }

    public function getReadingStartDate(): ?\DateTimeInterface
    {
        return $this->readingStartDate;
    }

    public function setReadingStartDate(?\DateTimeInterface $readingStartDate): static
    {
        $this->readingStartDate = $readingStartDate;

        return $this;
    }

    public function getReadingEndDate(): ?\DateTimeInterface
    {
        return $this->readingEndDate;
    }

    public function setReadingEndDate(?\DateTimeInterface $readingEndDate): static
    {
        $this->readingEndDate = $readingEndDate;

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
}
