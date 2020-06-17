<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObiektRepository")
 * @ORM\Table(name="obiekty")
 */
class Obiekt implements \JsonSerializable
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
    private ?string $symbol = "";

    /**
     * @ORM\Column(name="nazwa", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $nazwa = "";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GrupaObiektow", inversedBy="obiekty")
     * @ORM\JoinColumn(name="grupa_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private ?GrupaObiektow $grupa = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parametr", mappedBy="obiekt", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private Collection $parametry;

    /**
     * @ORM\Column(name="dlugosc", type="float", nullable=false)
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/\d+(\.\d+)?/")
     */
    private ?float $dlugosc = null;

    /**
     * @ORM\Column(name="szerokosc", type="float", nullable=false)
     * @Assert\NotNull()
     * @Assert\Regex(pattern="/\d+(\.\d+)?/")
     */
    private ?float $szerokosc = null;

    public function __construct()
    {
        $this->parametry = new ArrayCollection();
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

    public function getGrupa(): ?GrupaObiektow
    {
        return $this->grupa;
    }

    public function setGrupa(?GrupaObiektow $grupa): void
    {
        $this->grupa = $grupa;
    }

    public function getParametry(): Collection
    {
        return $this->parametry;
    }

    public function setParametry(Collection $parametry): void
    {
        $this->parametry = $parametry;
    }

    public function addParametry(Parametr $parametr): bool
    {
        if (!$this->parametry->contains($parametr)) {
            $parametr->setObiekt($this);
            return $this->parametry->add($parametr);
        }
        return false;
    }

    public function removeParametry(Parametr $parametr): bool
    {
        return $this->parametry->removeElement($parametr);
    }

    public function getDlugosc(): ?float
    {
        return $this->dlugosc;
    }

    public function setDlugosc(?float $dlugosc): void
    {
        $this->dlugosc = $dlugosc;
    }

    public function getSzerokosc(): ?float
    {
        return $this->szerokosc;
    }

    public function setSzerokosc(?float $szerokosc): void
    {
        $this->szerokosc = $szerokosc;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}