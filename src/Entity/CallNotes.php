<?php

namespace App\Entity;

use App\Repository\CallNotesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CallNotesRepository::class)
 */
class CallNotes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity=Calls::class, inversedBy="notes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $call_id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $sent_by;

    /**
     * @ORM\Column(type="datetime")
     */
    private $sent_on;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getCallId(): ?Calls
    {
        return $this->call_id;
    }

    public function setCallId(?Calls $call_id): self
    {
        $this->call_id = $call_id;

        return $this;
    }

    public function getSentBy(): ?Users
    {
        return $this->sent_by;
    }

    public function setSentBy(?Users $sent_by): self
    {
        $this->sent_by = $sent_by;

        return $this;
    }

    public function getSentOn(): ?\DateTimeInterface
    {
        return $this->sent_on;
    }

    public function setSentOn(\DateTimeInterface $sent_on): self
    {
        $this->sent_on = $sent_on;

        return $this;
    }
}
