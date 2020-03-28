<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GrupaObiektowRepository")
 * @ORM\Table(name="grupy_obiektow")
 */
class GrupaObiektow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    public int $id;

    /**
     * @ORM\Column(name="symbol", type="string", nullable=false)
     */
    public string $symbol;

    /**
     * @ORM\Column(name="nazwa", type="string", nullable=false)
     */
    public string $nazwa;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TypParametru")
     * @ORM\JoinTable(name="grupy_obiektow_typy_parametrow",
     *     joinColumns={@ORM\JoinColumn(name="grupa_obiektow_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="typ_parametru_id", referencedColumnName="id")}
     *     )
     */
    public ArrayCollection $typyParametrow;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Obiekt", mappedBy="grupa")
     */
    public ArrayCollection $obiekty;

    public function __construct()
    {
        $this->typyParametrow = new ArrayCollection();
        $this->obiekty = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    public function getNazwa(): string
    {
        return $this->nazwa;
    }

    public function setNazwa(string $nazwa): void
    {
        $this->nazwa = $nazwa;
    }

    public function getTypyParametrow(): ArrayCollection
    {
        return $this->typyParametrow;
    }

    public function setTypyParametrow(ArrayCollection $typyParametrow): void
    {
        $this->typyParametrow = $typyParametrow;
    }

    public function getObiekty(): ArrayCollection
    {
        return $this->obiekty;
    }

    public function setObiekty(ArrayCollection $obiekty): void
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

    public function addObiekt(Obiekt $obiekt):bool {
        if(!$this->obiekty->contains($obiekt)) {
            return $this->obiekty->add($obiekt);
        }
        return false;
    }

    public function removeObiekt(Obiekt $obiekt):bool {
        return $this->obiekty->removeElement($obiekt);
    }
}