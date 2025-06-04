<?php

namespace App\Entity;

use App\Repository\ArtisanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Message;

#[ORM\Entity(repositoryClass: ArtisanRepository::class)]
class Artisan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: "artisan", cascade: ["persist", "remove"])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $speciality = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $available = true; // ✅ Ajout de `nullable: false`

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'recipient', cascade: ['persist', 'remove'])]
    private Collection $messages;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->available = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): static
    {
        $this->speciality = $speciality;
        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): static // ✅ Correction du type en bool
    {
        $this->available = $available;
        return $this;
    }

    /** @return Collection<int, Message> */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRecipient($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getRecipient() === $this) {
                $message->setRecipient(null);
            }
        }
        return $this;
    }

    // ✅ Optimisation de `__toString()`
    public function __toString(): string
    {
        return sprintf(
            '%s - %s [%s]',
            $this->user ? $this->user->getFirstName() : 'Inconnu',
            $this->speciality ?: 'Spécialité inconnue',
            $this->available ? 'Disponible' : 'Indisponible'
        );
    }
}