<?php

namespace App\Entity;

use App\Repository\GenresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenresRepository::class)]
class Genres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    #[ORM\ManyToMany(targetEntity: Serie::class, inversedBy: 'genres')]
    private Collection $serie;

    public function __construct()
    {
        $this->serie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameEng(): ?string
    {
        return $this->nameEng;
    }

    public function setNameEng(string $nameEng): static
    {
        $this->nameEng = $nameEng;

        return $this;
    }

    public function getNameFra(): ?string
    {
        return $this->nameFra;
    }

    public function setNameFra(string $nameFra): static
    {
        $this->nameFra = $nameFra;

        return $this;
    }

    /**
     * @return Collection<int, Serie>
     */
    public function getSerie(): Collection
    {
        return $this->serie;
    }

    public function addSerie(Serie $serie): static
    {
        if (!$this->serie->contains($serie)) {
            $this->serie->add($serie);
        }

        return $this;
    }

    public function removeSerie(Serie $serie): static
    {
        $this->serie->removeElement($serie);

        return $this;
    }
}
