<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="username", type="string", nullable=false, length=24)
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="24")
     */
    private string $username = '';

    /**
     * @Assert\Regex(pattern="/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])/")
     * @Assert\NotCompromisedPassword()
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private string $password;

    /**
     * @ORM\Column(name="password_request_token", type="string", nullable=true)
     */
    private ?string $passwordRequestToken = null;

    /**
     * @ORM\Column(name="email", type="string", nullable=false, length=64)
     * @Assert\Length(min="10",max="64")
     * @Assert\Email()
     */
    private string $email = '';

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private ArrayCollection $roles;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GrupaObiektow", inversedBy="users")
     * @ORM\JoinTable(name="users_grupy_obiektow")
     */
    private Collection $grupyObiektow;

    /**
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private bool $enabled;

    public function __construct()
    {
        $this->roles = new ArrayCollection(['ROLE_USER']);
        $this->grupyObiektow = new ArrayCollection();
    }

    public function setRoles(array $roles): void
    {
        $this->roles = new ArrayCollection($roles);
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function addRole(string $role): bool
    {
        if (!$this->roles->contains($role)) {
            return $this->roles->add($role);
        }
        return false;
    }

    public function removeRole(string $role): bool
    {
        return $this->roles->removeElement($role);
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPasswordRequestToken(): ?string
    {
        return $this->passwordRequestToken;
    }

    public function setPasswordRequestToken(?string $passwordRequestToken): void
    {
        $this->passwordRequestToken = $passwordRequestToken;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getGrupyObiektow()
    {
        return $this->grupyObiektow;
    }

    public function setGrupyObiektow($grupyObiektow): void
    {
        $this->grupyObiektow = $grupyObiektow;
    }

    public function addGrupaObiektow(GrupaObiektow $grupaObiektow): bool
    {
        if (!$this->grupyObiektow->contains($grupaObiektow)) {
            return $this->grupyObiektow->add($grupaObiektow);
        }
    }

    public function removeGrupaObiektow(GrupaObiektow $grupaObiektow): bool
    {
        return $this->grupyObiektow->removeElement($grupaObiektow);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}