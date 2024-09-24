<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $plexName;

    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: "user")]
    private $episodeShows;

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    private $movies;

    public function __construct()
    {
        $this->episodeShows = new ArrayCollection();
        $this->movies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlexName(): ?string
    {
        return $this->plexName;
    }

    public function setPlexName(string $plexName): self
    {
        $this->plexName = $plexName;

        return $this;
    }

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: "user")]
    public function getEpisodeShows(): Collection
    {
        return $this->episodeShows;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: "user")]
    public function addEpisodeShow(Episode $episodeShow): self
    {
        if (!$this->episodeShows->contains($episodeShow)) {
            $this->episodeShows[] = $episodeShow;
            $episodeShow->setUser($this);
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: "user")]
    public function removeEpisodeShow(Episode $episodeShow): self
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getUser() === $this) {
                $episodeShow->setUser(null);
            }
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->setUser($this);
        }

        return $this;
    }

    #[ORM\OneToMany(targetEntity: Movie::class, mappedBy: "user")]
    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getUser() === $this) {
                $movie->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->plexName;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->plexName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
