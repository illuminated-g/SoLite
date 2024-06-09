<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints;

use App\Validation\UserValidator;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('username', message: 'Username must be unique')]
#[UniqueEntity('email', message: 'Email must be unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Constraints\NotBlank]
    #[Constraints\Length (min: 4)]
    #[Constraints\Regex(
        pattern: '/[\[\]!@#\$%\^&\*()\\/]/',
        match: false,
        message: 'Username contains illegal characters'
    )]

    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank]
    #[Constraints\Regex(
        pattern: '/[\[\]!@#\$%\^&\*()\\/]/',
        match: false,
        message: 'Full name contains illegal characters'
    )]
    private ?string $full_name = null;

    #[ORM\Column(length: 255)]
    #[Constraints\NotBlank]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Constraints\Regex(
        pattern: '/[\[\]!@#\$%\^&\*()\\/]/',
        match: false,
        message: 'Country contains illegal characters'
    )]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Constraints\Regex(
        pattern: '/[\[\]!@#\$%\^&\*()\\/]/',
        match: false,
        message: 'State contains illegal characters'
    )]
    private ?string $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Constraints\Regex(
        pattern: '/[\[\]!@#\$%\^&\*()\\/]/',
        match: false,
        message: 'Company contains illegal characters'
    )]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ni_email = null;

    #[ORM\Column]#[Constraints\Expression('this.isNiEmployee() != this.isChampion() or !this.isChampion()', message: 'NI Employees cannot also be Champions')]
    private bool $champion = false;

    #[ORM\Column]
    private bool $ni_employee = false;

    #[ORM\Column]
    #[Constraints\Expression('this.isNiEmployee() != this.isPartner() or !this.isPartner()', message: 'NI Employees cannot also be Champions')]
    private bool $partner = false;

    #[ORM\Column]
    private bool $clad = false;

    #[ORM\Column]
    private bool $cld = false;

    #[ORM\Column]
    private bool $cla = false;

    #[ORM\Column]
    private bool $ctd = false;

    #[ORM\Column]
    private bool $cta = false;

    #[ORM\Column]
    private bool $cled = false;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Submission::class)]
    private Collection $submissions;

    #[ORM\Column]
    private bool $change_pass = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registered_on = null;

    #[ORM\Column(length: 20)]
    private ?string $registration_ip = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_login = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $temporary_key = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $temp_key_expires = null;

    public function __construct()
    {
        $this->submissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): static
    {
        $this->full_name = $full_name;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function isChampion(): bool
    {
        return $this->champion;
    }

    public function setChampion(bool $champion): static
    {
        $this->champion = $champion;

        return $this;
    }

    public function isNiEmployee(): bool
    {
        return $this->ni_employee;
    }

    public function setNiEmployee(bool $ni_employee): static
    {
        $this->ni_employee = $ni_employee;

        return $this;
    }

    public function isPartner(): bool
    {
        return $this->partner;
    }

    public function setPartner(bool $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    public function isClad(): bool
    {
        return $this->clad;
    }

    public function setClad(bool $clad): static
    {
        $this->clad = $clad;

        return $this;
    }

    public function isCld(): bool
    {
        return $this->cld;
    }

    public function setCld(bool $cld): static
    {
        $this->cld = $cld;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getNiEmail(): ?string
    {
        return $this->ni_email;
    }

    public function setNiEmail(?string $ni_email): static
    {
        $this->ni_email = $ni_email;

        return $this;
    }

    public function isCla(): bool
    {
        return $this->cla;
    }

    public function setCla(bool $cla): static
    {
        $this->cla = $cla;

        return $this;
    }

    public function isCtd(): bool
    {
        return $this->ctd;
    }

    public function setCtd(bool $ctd): static
    {
        $this->ctd = $ctd;

        return $this;
    }

    public function isCta(): bool
    {
        return $this->cta;
    }

    public function setCta(bool $cta): static
    {
        $this->cta = $cta;

        return $this;
    }

    public function isCled(): bool
    {
        return $this->cled;
    }

    public function setCled(bool $cled): static
    {
        $this->cled = $cled;

        return $this;
    }

    /**
     * @return Collection<int, Submission>
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): static
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions->add($submission);
            $submission->setParticipant($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): static
    {
        if ($this->submissions->removeElement($submission)) {
            // set the owning side to null (unless already changed)
            if ($submission->getParticipant() === $this) {
                $submission->setParticipant(null);
            }
        }

        return $this;
    }

    public function isChangePass(): ?bool
    {
        return $this->change_pass;
    }

    public function setChangePass(bool $change_pass): static
    {
        $this->change_pass = $change_pass;

        return $this;
    }

    public function getRegisteredOn(): ?\DateTimeInterface
    {
        return $this->registered_on;
    }

    public function setRegisteredOn(\DateTimeInterface $registered_on): static
    {
        $this->registered_on = $registered_on;

        return $this;
    }

    public function getRegistrationIp(): ?string
    {
        return $this->registration_ip;
    }

    public function setRegistrationIp(string $registration_ip): static
    {
        $this->registration_ip = $registration_ip;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $last_login): static
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getTemporaryKey(): ?string
    {
        return $this->temporary_key;
    }

    public function setTemporaryKey(?string $temporary_key): static
    {
        $this->temporary_key = $temporary_key;

        return $this;
    }

    public function getTempKeyExpires(): ?\DateTimeInterface
    {
        return $this->temp_key_expires;
    }

    public function setTempKeyExpires(?\DateTimeInterface $temp_key_expires): static
    {
        $this->temp_key_expires = $temp_key_expires;

        return $this;
    }
}
