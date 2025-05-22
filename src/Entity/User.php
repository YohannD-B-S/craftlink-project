<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255 )]
    private ?string $lastName = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $postal_code = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    private ?string $phone_number = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255,)]
    private array $role = [];

    #[ORM\Column(nullable: true)]
    private ?\DateTime $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $update_date = null;

    public function CreateArtisan(string $email, string $passwordHash, string $firstName, string $lastName, \DateTime $created_at): void
    {
        $this->lastName=$lastName;
        $this->firstName=$firstName;
        $this->email = $email;
        $this->password = $passwordHash;
        $this->created_at = new \DateTime();
        $this->role = ['ROLE_ARTISAN'];
    }

    public function CreateClient (string $email, string $passwordHash, string $firstName, string $lastName, \DateTime $created_at): void
    {
        $this->lastName=$lastName;
        $this->firstName=$firstName;
        $this->email = $email;
        $this->password = $passwordHash;
        $this->created_at = new \DateTime();
        $this->role = ['ROLE_CLIENT'];
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function getRoles(): array
    {
        // Garantie qu'un utilisateur a toujours au moins ROLE_USER
        $role = $this->role;


        return array_unique($role);
    }


    public function setRoles(array $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function setRole(array $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des données sensibles temporaires, efface-les ici (ex: $this->plainPassword = null)
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    public function setUpdateDate(?\DateTime $update_date): static
    {
        $this->update_date = $update_date;

        return $this;
    }


}
