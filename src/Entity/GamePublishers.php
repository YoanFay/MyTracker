<?php

namespace App\Entity;

use App\Repository\GamePublishersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePublishersRepository::class)]
class GamePublishers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /** @var Collection<int, Game> $games */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'publishers')]
    private Collection $games;

    #[ORM\Column]
    private ?int $igdbId = null;


    public function __construct()
    {

        $this->games = new ArrayCollection();
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
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {

        return $this->games;
    }


    public function addGame(Game $game): static
    {

        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addPublisher($this);
        }

        return $this;
    }


    public function removeGame(Game $game): static
    {

        if ($this->games->removeElement($game)) {
            $game->removePublisher($this);
        }

        return $this;
    }


    public function getIgdbId(): ?int
    {

        return $this->igdbId;
    }


    public function setIgdbId(int $igdbId): static
    {

        $this->igdbId = $igdbId;

        return $this;
    }
}
