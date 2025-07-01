<?php

namespace App\Entity;

use App\Repository\TagsTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsTypeRepository::class)]
class TagsType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    /** @var Collection<int, Tags> $tags */
    #[ORM\OneToMany(mappedBy: 'tagsType', targetEntity: Tags::class)]
    private Collection $tags;


    public function __construct()
    {

        $this->tags = new ArrayCollection();
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


    public function setNameEng(?string $nameEng): static
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
            $tag->setTagsType($this);
        }

        return $this;
    }
}
