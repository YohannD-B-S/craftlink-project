<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Artisan;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: "author")]
    private Collection $articles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $speciality = null;

    #[ORM\OneToOne(targetEntity: Artisan::class, mappedBy: "user", cascade: ["persist", "remove"])]
    private ?Artisan $artisan = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    // Obligatoire pour UserInterface
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    public function eraseCredentials(): void
    {
        // Supprime les données sensibles temporaires si besoin
    }

    // Gestion des rôles utilisateur
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getRoleLabel(): string
    {
        $roleLabels = [
            'ROLE_CLIENT' => 'Client',
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_ARTISAN' => 'Artisan',
        ];

        foreach ($this->getRoles() as $role) {
            if (isset($roleLabels[$role])) {
                return $roleLabels[$role];
            }
        }

        return 'Utilisateur';
    }

    // Création d'un artisan
    public function createArtisan(
        string $email,
        string $passwordHash,
        string $firstName,
        string $speciality,
        string $phone,
        string $postalCode,
        string $address,
        string $lastName,
        \DateTime $createdAt,
        EntityManagerInterface $entityManager
    ): void {

        // Vérifier qu'un artisan n'existe pas déjà pour cet utilisateur
        if ($this->artisan !== null) {
            throw new \Exception("Cet utilisateur est déjà un artisan.");
        }

        // Définition des informations utilisateur
        $this->email = $email;
        $this->password = $passwordHash;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->speciality = $speciality;
        $this->phoneNumber = $phone;
        $this->postalCode = $postalCode;
        $this->address = $address;
        $this->createdAt = $createdAt;
        $this->roles = ['ROLE_ARTISAN'];

        // Création et association d'un artisan
        $artisan = new Artisan();
        $artisan->setUser($this);
        $artisan->setSpeciality($speciality);
        $artisan->setAvailable("Disponible");

        // Persistance en base
        $entityManager->persist($this);
        $entityManager->persist($artisan);
        $entityManager->flush();
    }

    public function createClient(
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        string $address,
        string $postalCode,
        string $phoneNumber,
        \DateTime $createdAt
    ): void {
        $this->email = $email;
        $this->password = $passwordHash;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->phoneNumber = $phoneNumber;
        $this->createdAt = $createdAt;

        $this->roles = ['ROLE_CLIENT']; // Aucun Artisan associé
    }

    // Gestion des données utilisateur
    public function getId(): ?int { return $this->id; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): self { $this->firstName = $firstName; return $this; }
    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): self { $this->lastName = $lastName; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(string $address): self { $this->address = $address; return $this; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(string $postalCode): self { $this->postalCode = $postalCode; return $this; }
    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(string $phoneNumber): self { $this->phoneNumber = $phoneNumber; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function getCreatedAt(): ?\DateTime { return $this->createdAt; }
    public function setCreatedAt(\DateTime $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTime { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTime $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
    public function getSpeciality(): ?string { return $this->speciality; }
    public function setSpeciality(?string $speciality): self { $this->speciality = $speciality; return $this; }
    public function getArtisan(): ?Artisan { return $this->artisan; }
    public function setArtisan(?Artisan $artisan): self { $this->artisan = $artisan; return $this; }
}