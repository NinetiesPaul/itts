<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipmentRepository::class)
 */
class Equipment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sn;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_part;

    /**
     * @ORM\ManyToOne(targetEntity=EquipmentType::class, inversedBy="type_id")
     * @ORM\JoinColumn(nullable=false)
     */
    private $equipmentType;

    /**
     * @ORM\ManyToOne(targetEntity=Equipment::class)
     */
    private $has_parent;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="equipment")
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity=Calls::class, mappedBy="equipment")
     */
    private $calls;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): self
    {
        $this->sn = $sn;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getIsPart(): ?bool
    {
        return $this->is_part;
    }

    public function setIsPart(?bool $is_part): self
    {
        $this->is_part = $is_part;

        return $this;
    }

    public function getEquipmentType(): ?EquipmentType
    {
        return $this->equipmentType;
    }

    public function setEquipmentType(?EquipmentType $equipmentType): self
    {
        $this->equipmentType = $equipmentType;

        return $this;
    }

    public function getHasParent(): ?self
    {
        return $this->has_parent;
    }

    public function setHasParent(?self $has_parent): self
    {
        $this->has_parent = $has_parent;

        return $this;
    }

    public function getUserId(): ?Users
    {
        return $this->user_id;
    }

    public function setUserId(?Users $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Calls>
     */
    public function getCalls(): Collection
    {
        return $this->calls;
    }

    public function addCall(Calls $call): self
    {
        if (!$this->calls->contains($call)) {
            $this->calls[] = $call;
            $call->setEquipment($this);
        }

        return $this;
    }

    public function removeCall(Calls $call): self
    {
        if ($this->calls->removeElement($call)) {
            // set the owning side to null (unless already changed)
            if ($call->getEquipment() === $this) {
                $call->setEquipment(null);
            }
        }

        return $this;
    }
}
