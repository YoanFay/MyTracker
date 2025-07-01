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
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $plexName;

    /** @var array<string> $roles */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private string $password;


    public function getId(): int
    {

        return $this->id;
    }


    public function setId(int $id): self
    {

        $this->id = $id;

        return $this;
    }


    public function getPlexName(): string
    {

        return $this->plexName;
    }


    public function setPlexName(string $plexName): self
    {

        $this->plexName = $plexName;

        return $this;
    }


    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {

        return (string)$this->plexName;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {

        return (string)$this->plexName;
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



    /** @param array<string> $roles */
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
