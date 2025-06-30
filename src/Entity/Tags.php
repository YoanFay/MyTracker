<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    /** @var Collection<int, Serie> $series */
    #[ORM\ManyToMany(targetEntity: Serie::class, inversedBy: 'tags')]
    private Collection $series;

    #[ORM\ManyToOne(inversedBy: 'tags')]
    #[ORM\JoinColumn(nullable: false)]
    private TagsType $tagsType;


    public function __construct()
    {

        $this->series = new ArrayCollection();
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


    public function setNameFra(?string $nameFra): static
    {

        $this->nameFra = $nameFra;

        return $this;
    }


    /**
     * @return Collection<int, Serie>
     */
    public function getSeries(): Collection
    {

        return $this->series;
    }


    public function addSeries(Serie $series): static
    {

        if (!$this->series->contains($series)) {
            $this->series->add($series);
        }

        return $this;
    }


    public function removeSeries(Serie $series): static
    {

        $this->series->removeElement($series);

        return $this;
    }


    public function getTagsType(): TagsType
    {

        return $this->tagsType;
    }


    public function setTagsType(TagsType $tagsType): static
    {

        $this->tagsType = $tagsType;

        return $this;
    }
}
