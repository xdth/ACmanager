<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Server", mappedBy="owner")
     */
    private $serversOwned;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Server", mappedBy="admins")
     */
    private $serversAdmin;

    public function __construct()
    {
        $this->serversOwned = new ArrayCollection();
        $this->serversAdmin = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * @return Collection|Server[]
     */
    public function getServersOwned(): Collection
    {
        return $this->serversOwned;
    }

    public function addServersOwned(Server $serversOwned): self
    {
        if (!$this->serversOwned->contains($serversOwned)) {
            $this->serversOwned[] = $serversOwned;
            $serversOwned->setOwner($this);
        }

        return $this;
    }

    public function removeServersOwned(Server $serversOwned): self
    {
        if ($this->serversOwned->contains($serversOwned)) {
            $this->serversOwned->removeElement($serversOwned);
            // set the owning side to null (unless already changed)
            if ($serversOwned->getOwner() === $this) {
                $serversOwned->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Server[]
     */
    public function getServersAdmin(): Collection
    {
        return $this->serversAdmin;
    }

    public function addServersAdmin(Server $serversAdmin): self
    {
        if (!$this->serversAdmin->contains($serversAdmin)) {
            $this->serversAdmin[] = $serversAdmin;
            $serversAdmin->addAdmin($this);
        }

        return $this;
    }

    public function removeServersAdmin(Server $serversAdmin): self
    {
        if ($this->serversAdmin->contains($serversAdmin)) {
            $this->serversAdmin->removeElement($serversAdmin);
            $serversAdmin->removeAdmin($this);
        }

        return $this;
    }

}
