<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GrupaObiektowRepository")
 * @ORM\Table(name="grupy_obiektow")
 */
class GrupaObiektow implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="symbol", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $symbol = null;

    /**
     * @ORM\Column(name="nazwa", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $nazwa = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TypParametru", inversedBy="grupyObiektow")
     * @ORM\JoinTable(name="grupy_obiektow_typy_parametrow")
     */
    private Collection $typyParametrow;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Obiekt", mappedBy="grupa")
     */
    private Collection $obiekty;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="grupyObiektow")
     */
    private Collection $users;

    /**
     * @ORM\Column(name="ostatnia_aktualizacja", type="datetime", nullable=true)
     */
    private ?\DateTime $ostatniaAktualizacja = null;

    /**
     * @ORM\Column(name="usunieta", type="boolean", nullable=false)
     */
    private bool $usunieta = false;

    public function __construct()
    {
        $this->typyParametrow = new ArrayCollection();
        $this->obiekty = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->ostatniaAktualizacja ??= new \DateTime('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(?string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    public function setNazwa(?string $nazwa): void
    {
        $this->nazwa = $nazwa;
    }

    public function getTypyParametrow(): Collection
    {
        return $this->typyParametrow;
    }

    public function setTypyParametrow(Collection $typyParametrow): void
    {
        $this->typyParametrow = $typyParametrow;
    }

    public function getObiekty(): Collection
    {
        return $this->obiekty;
    }

    public function setObiekty(Collection $obiekty): void
    {
        $this->obiekty = $obiekty;
    }

    public function addTypParametru(TypParametru $typParametru):bool {
        if(!$this->typyParametrow->contains($typParametru)) {
            return $this->typyParametrow->add($typParametru);
        }
        return false;
    }

    public function removeTypParametru(TypParametru $typParametru):bool {
        return $this->typyParametrow->removeElement($typParametru);
    }

    public function addObiekt(Obiekt $obiekt): bool
    {
        if (!$this->obiekty->contains($obiekt)) {
            return $this->obiekty->add($obiekt);
        }
        return false;
    }

    public function removeObiekt(Obiekt $obiekt): bool
    {
        return $this->obiekty->removeElement($obiekt);
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users): void
    {
        $this->users = $users;
    }

    public function addUser(User $user): bool
    {
        if (!$this->users->contains($user)) {
            return $this->users->add($user);
        }
        return false;
    }

    public function removeUser(User $user): bool
    {
        return $this->users->removeElement($user);
    }

    /**
     * @return \DateTime
     */
    public function getOstatniaAktualizacja(): \DateTime
    {
        return $this->ostatniaAktualizacja;
    }

    /**
     * @param \DateTime $ostatniaAktualizacja
     */
    public function setOstatniaAktualizacja(\DateTime $ostatniaAktualizacja): void
    {
        $this->ostatniaAktualizacja = $ostatniaAktualizacja;
    }

    /**
     * @return bool
     */
    public function isUsunieta(): bool
    {
        return $this->usunieta;
    }

    /**
     * @param bool $usunieta
     */
    public function setUsunieta(bool $usunieta): void
    {
        $this->usunieta = $usunieta;
    }



    public function jsonSerialize()
    {
//        return get_object_vars($this);
        return [
            'grupaObiektowId' => $this->id,
            'nazwa' => $this->nazwa,
            'symbol' => $this->symbol,
            'usunieta' => $this->usunieta,
            'ostatniaAktualizacja' => $this->ostatniaAktualizacja != null ? $this->ostatniaAktualizacja->format('Y-m-d H:i:s') : '1900-01-01 00:00',
            'typyParametrow' => $this->typyParametrow->getValues(),
        ];
    }

}