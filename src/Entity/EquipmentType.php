<?php

namespace App\Entity;

use App\Repository\EquipmentTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EquipmentTypeRepository::class)
 */
class EquipmentType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=55)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Equipment::class, mappedBy="equipmentType", orphanRemoval=true)
     */
    private $type_id;

    public function __construct()
    {
        $this->type_id = new ArrayCollection();
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

    /**
     * @return Collection<int, Equipment>
     */
    public function getTypeId(): Collection
    {
        return $this->type_id;
    }

    public function addTypeId(Equipment $typeId): self
    {
        if (!$this->type_id->contains($typeId)) {
            $this->type_id[] = $typeId;
            $typeId->setEquipmentType($this);
        }

        return $this;
    }

    public function removeTypeId(Equipment $typeId): self
    {
        if ($this->type_id->removeElement($typeId)) {
            // set the owning side to null (unless already changed)
            if ($typeId->getEquipmentType() === $this) {
                $typeId->setEquipmentType(null);
            }
        }

        return $this;
    }
}
