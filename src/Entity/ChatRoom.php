<?php

namespace App\Entity;

use App\Repository\ChatRoomRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChatRoomRepository::class)]
class ChatRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userOne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userTwo = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Passe à true quand les deux personnes ont accepté de révéler leur identité.
    #[ORM\Column(options: ['default' => false])]
    private bool $isRevealed = false;

    public function getId(): ?int { return $this->id; }

    public function getUserOne(): ?User { return $this->userOne; }
    public function setUserOne(?User $user): static { $this->userOne = $user; return $this; }

    public function getUserTwo(): ?User { return $this->userTwo; }
    public function setUserTwo(?User $user): static { $this->userTwo = $user; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $date): static { $this->createdAt = $date; return $this; }

    public function isRevealed(): bool { return $this->isRevealed; }
    public function setIsRevealed(bool $isRevealed): static { $this->isRevealed = $isRevealed; return $this; }
}