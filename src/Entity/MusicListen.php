<?php

namespace App\Entity;

use App\Repository\MusicListenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicListenRepository::class)]
class MusicListen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'musicListens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    #[ORM\Column]
    private ?DateTime $listenAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): static
    {
        $this->music = $music;

        return $this;
    }

    public function getListenAt(): ?DateTime
    {
        return $this->listenAt;
    }

    public function setListenAt(DateTime $listenAt): static
    {
        $this->listenAt = $listenAt;

        return $this;
    }
}
