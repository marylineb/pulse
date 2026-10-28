<?php

namespace App\Entity;

use App\Repository\RevealRequestRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevealRequestRepository::class)]
class RevealRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ChatRoom $room = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $requestedBy = null;

    #[ORM\Column]
    private bool $acceptedByRequester = true;

    #[ORM\Column]
    private bool $acceptedByOther = false;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $revealedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->acceptedByRequester = true;
        $this->acceptedByOther = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?ChatRoom
    {
        return $this->room;
    }

    public function setRoom(?ChatRoom $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?User $requestedBy): static
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    public function isAcceptedByRequester(): bool
    {
        return $this->acceptedByRequester;
    }

    public function setAcceptedByRequester(bool $acceptedByRequester): static
    {
        $this->acceptedByRequester = $acceptedByRequester;

        return $this;
    }

    public function isAcceptedByOther(): bool
    {
        return $this->acceptedByOther;
    }

    public function setAcceptedByOther(bool $acceptedByOther): static
    {
        $this->acceptedByOther = $acceptedByOther;

        return $this;
    }

    public function getRevealedAt(): ?\DateTimeImmutable
    {
        return $this->revealedAt;
    }

    public function setRevealedAt(?\DateTimeImmutable $revealedAt): static
    {
        $this->revealedAt = $revealedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}