<?php

namespace App\Entity;

use App\Repository\SerieUpdateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieUpdateRepository::class)]
class SerieUpdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'serieUpdates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Serie $serie = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oldStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $newStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $oldNextAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $newNextAired = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextAiredType = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oldAiredType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOldStatus(): ?string
    {
        return $this->oldStatus;
    }

    public function setOldStatus(?string $oldStatus): static
    {
        $this->oldStatus = $oldStatus;

        return $this;
    }

    public function getNewStatus(): ?string
    {
        return $this->newStatus;
    }

    public function setNewStatus(?string $newStatus): static
    {
        $this->newStatus = $newStatus;

        return $this;
    }

    public function getOldNextAired(): ?\DateTimeInterface
    {
        return $this->oldNextAired;
    }

    public function setOldNextAired(?\DateTimeInterface $oldNextAired): static
    {
        $this->oldNextAired = $oldNextAired;

        return $this;
    }

    public function getNewNextAired(): ?\DateTimeInterface
    {
        return $this->newNextAired;
    }

    public function setNewNextAired(?\DateTimeInterface $newNextAired): static
    {
        $this->newNextAired = $newNextAired;

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

    public function getOldAiredType(): ?string
    {
        return $this->oldAiredType;
    }

    public function setOldAiredType(?string $oldAiredType): static
    {
        $this->oldAiredType = $oldAiredType;

        return $this;
    }
}
