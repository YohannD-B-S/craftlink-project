<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'conversations')]
    private ?User $participantOne = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'conversations')]
    private ?User $participantTwo = null;

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'conversation', cascade: ['remove'])]
    private Collection $messages;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'pending'; // ✅ La conversation est en attente par défaut

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipantOne(): ?User
    {
        return $this->participantOne;
    }

    public function setParticipantOne(?User $participantOne): static
    {
        $this->participantOne = $participantOne;
        return $this;
    }

    public function getParticipantTwo(): ?User
    {
        return $this->participantTwo;
    }

    public function setParticipantTwo(?User $participantTwo): static
    {
        $this->participantTwo = $participantTwo;
        return $this;
    }

    /** @return Collection<int, Message> */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message) && $this->status === 'accepted') { // ✅ Ajout uniquement si la conversation est active
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}