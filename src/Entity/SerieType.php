<?php

namespace App\Entity;

use App\Repository\SerieTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieTypeRepository::class)]
class SerieType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'serieType', targetEntity: Serie::class)]
    private Collection $series;

    public function __construct()
    {
        $this->series = new ArrayCollection();
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
            $series->setSerieType($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): static
    {
        if ($this->series->removeElement($series)) {
            // set the owning side to null (unless already changed)
            if ($series->getSerieType() === $this) {
                $series->setSerieType(null);
            }
        }

        return $this;
    }
}
