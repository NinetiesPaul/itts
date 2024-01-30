<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

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
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Department::class, inversedBy="department_id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;

    /**
     * @ORM\OneToMany(targetEntity=Equipment::class, mappedBy="user_id")
     */
    private $equipment;

    /**
     * @ORM\OneToMany(targetEntity=Calls::class, mappedBy="opened_by")
     */
    private $calls_opened;

    /**
     * @ORM\OneToMany(targetEntity=Calls::class, mappedBy="answered_by")
     */
    private $calls_answered;

    /**
     * @ORM\OneToMany(targetEntity=Calls::class, mappedBy="closed_by")
     */
    private $calls_closed;

    public function __construct()
    {
        $this->equipment = new ArrayCollection();
        $this->calls_opened = new ArrayCollection();
        $this->calls_answered = new ArrayCollection();
        $this->calls_closed = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

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
        return $this->password;
    }

    public function setPassword(string $password): self
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
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Collection<int, Equipment>
     */
    public function getEquipment(): Collection
    {
        return $this->equipment;
    }

    public function addEquipment(Equipment $equipment): self
    {
        if (!$this->equipment->contains($equipment)) {
            $this->equipment[] = $equipment;
            $equipment->setUserId($this);
        }

        return $this;
    }

    public function removeEquipment(Equipment $equipment): self
    {
        if ($this->equipment->removeElement($equipment)) {
            // set the owning side to null (unless already changed)
            if ($equipment->getUserId() === $this) {
                $equipment->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calls>
     */
    public function getCallsOpened(): Collection
    {
        return $this->calls_opened;
    }

    public function addCallsOpened(Calls $callsOpened): self
    {
        if (!$this->calls_opened->contains($callsOpened)) {
            $this->calls_opened[] = $callsOpened;
            $callsOpened->setOpenedBy($this);
        }

        return $this;
    }

    public function removeCallsOpened(Calls $callsOpened): self
    {
        if ($this->calls_opened->removeElement($callsOpened)) {
            // set the owning side to null (unless already changed)
            if ($callsOpened->getOpenedBy() === $this) {
                $callsOpened->setOpenedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calls>
     */
    public function getCallsAnswered(): Collection
    {
        return $this->calls_answered;
    }

    public function addCallsAnswered(Calls $callsAnswered): self
    {
        if (!$this->calls_answered->contains($callsAnswered)) {
            $this->calls_answered[] = $callsAnswered;
            $callsAnswered->setAnsweredBy($this);
        }

        return $this;
    }

    public function removeCallsAnswered(Calls $callsAnswered): self
    {
        if ($this->calls_answered->removeElement($callsAnswered)) {
            // set the owning side to null (unless already changed)
            if ($callsAnswered->getAnsweredBy() === $this) {
                $callsAnswered->setAnsweredBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calls>
     */
    public function getCallsClosed(): Collection
    {
        return $this->calls_closed;
    }

    public function addCallsClosed(Calls $callsClosed): self
    {
        if (!$this->calls_closed->contains($callsClosed)) {
            $this->calls_closed[] = $callsClosed;
            $callsClosed->setClosedBy($this);
        }

        return $this;
    }

    public function removeCallsClosed(Calls $callsClosed): self
    {
        if ($this->calls_closed->removeElement($callsClosed)) {
            // set the owning side to null (unless already changed)
            if ($callsClosed->getClosedBy() === $this) {
                $callsClosed->setClosedBy(null);
            }
        }

        return $this;
    }
}
