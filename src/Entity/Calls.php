<?php

namespace App\Entity;

use App\Repository\CallsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CallsRepository::class)
 */
class Calls
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Equipment::class, inversedBy="calls")
     */
    private $equipment;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="calls_opened")
     * @ORM\JoinColumn(nullable=false)
     */
    private $opened_by;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="calls_answered")
     * @ORM\JoinColumn(nullable=true)
     */
    private $answered_by;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="calls_closed")
     * @ORM\JoinColumn(nullable=true)
     */
    private $closed_by;

    /**
     * @ORM\OneToMany(targetEntity=CallNotes::class, mappedBy="call_id")
     */
    private $notes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $opened_on;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $answered_on;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closed_on;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function getOpenedBy(): ?Users
    {
        return $this->opened_by;
    }

    public function setOpenedBy(?Users $opened_by): self
    {
        $this->opened_by = $opened_by;

        return $this;
    }

    public function getAnsweredBy(): ?Users
    {
        return $this->answered_by;
    }

    public function setAnsweredBy(?Users $answered_by): self
    {
        $this->answered_by = $answered_by;

        return $this;
    }

    public function getClosedBy(): ?Users
    {
        return $this->closed_by;
    }

    public function setClosedBy(?Users $closed_by): self
    {
        $this->closed_by = $closed_by;

        return $this;
    }

    /**
     * @return Collection<int, CallNotes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(CallNotes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setCallId($this);
        }

        return $this;
    }

    public function removeNote(CallNotes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getCallId() === $this) {
                $note->setCallId(null);
            }
        }

        return $this;
    }

    public function getOpenedOn(): ?\DateTimeInterface
    {
        return $this->opened_on;
    }

    public function setOpenedOn(\DateTimeInterface $opened_on): self
    {
        $this->opened_on = $opened_on;

        return $this;
    }

    public function getAnsweredOn(): ?\DateTimeInterface
    {
        return $this->answered_on;
    }

    public function setAnsweredOn(?\DateTimeInterface $answered_on): self
    {
        $this->answered_on = $answered_on;

        return $this;
    }

    public function getClosedOn(): ?\DateTimeInterface
    {
        return $this->closed_on;
    }

    public function setClosedOn(?\DateTimeInterface $closed_on): self
    {
        $this->closed_on = $closed_on;

        return $this;
    }
}
