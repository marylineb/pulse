<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Choisis un pseudo.')]
    #[Assert\Length(min: 2, max: 50, minMessage: 'Ton pseudo doit faire au moins {{ limit }} caractères.', maxMessage: 'Ton pseudo ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $pseudo = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $firstname = null;

    // Application de rencontre : réservée aux personnes majeures.
    #[ORM\Column(nullable: true)]
    #[Assert\NotNull(message: 'Indique ton âge.')]
    #[Assert\Range(min: 18, max: 120, notInRangeMessage: 'Pulse est réservé aux personnes de {{ min }} ans et plus.')]
    private ?int $age = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'Ta bio ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $bio = null;

    // Seules les adresses https sont acceptées pour l'image de profil.
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: 'Entre une adresse d\'image valide (https).', protocols: ['https'], requireTld: true)]
    #[Assert\Length(max: 255)]
    private ?string $avatar = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $vibe = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Assert\Count(max: 10, maxMessage: '{{ limit }} centres d\'intérêt maximum.')]
    #[Assert\All([new Assert\Length(max: 30)])]
    private ?array $interests = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getVibe(): ?string
    {
        return $this->vibe;
    }

    public function setVibe(?string $vibe): static
    {
        $this->vibe = $vibe;
        return $this;
    }

    public function getInterests(): ?array
    {
        return $this->interests;
    }

    public function setInterests(?array $interests): static
    {
        $this->interests = $interests;
        return $this;
    }

    public function getInterestsArray(): array
    {
        if (!$this->interests) {
            return [];
        }

        if (is_array($this->interests)) {
            return array_values(array_filter(array_map(
                static fn ($interest) => mb_strtolower(trim((string) $interest)),
                $this->interests
            )));
        }

        return [];
    }
}