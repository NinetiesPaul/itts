<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepartmentRepository::class)
 */
class Department
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
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Users::class, mappedBy="department")
     */
    private $department_id;

    public function __construct()
    {
        $this->department_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getDepartmentId(): Collection
    {
        return $this->department_id;
    }

    public function addDepartmentId(Users $departmentId): self
    {
        if (!$this->department_id->contains($departmentId)) {
            $this->department_id[] = $departmentId;
            $departmentId->setDepartment($this);
        }

        return $this;
    }

    public function removeDepartmentId(Users $departmentId): self
    {
        if ($this->department_id->removeElement($departmentId)) {
            // set the owning side to null (unless already changed)
            if ($departmentId->getDepartment() === $this) {
                $departmentId->setDepartment(null);
            }
        }

        return $this;
    }
}
