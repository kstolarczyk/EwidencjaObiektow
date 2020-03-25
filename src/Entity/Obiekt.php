<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObiektRepository")
 * @ORM\Table(name="obiekty")
 */
class Obiekt
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
     * @ORM\ManyToOne(targetEntity="App\Entity\GrupaObiektow", inversedBy="obiekty")
     * @ORM\JoinColumn(name="grupa_id", referencedColumnName="id")
     */
    public GrupaObiektow $grupa;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parametr", mappedBy="obiekt")
     */
    public ArrayCollection $parametry;

    public function __construct()
    {
        $this->parametry = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }

    /**
     * @return string
     */
    public function getNazwa(): string
    {
        return $this->nazwa;
    }

    /**
     * @param string $nazwa
     */
    public function setNazwa(string $nazwa): void
    {
        $this->nazwa = $nazwa;
    }

    /**
     * @return GrupaObiektow
     */
    public function getGrupa(): GrupaObiektow
    {
        return $this->grupa;
    }

    /**
     * @param GrupaObiektow $grupa
     */
    public function setGrupa(GrupaObiektow $grupa): void
    {
        $this->grupa = $grupa;
    }

    /**
     * @return ArrayCollection
     */
    public function getParametry(): ArrayCollection
    {
        return $this->parametry;
    }

    /**
     * @param ArrayCollection $parametry
     */
    public function setParametry(ArrayCollection $parametry): void
    {
        $this->parametry = $parametry;
    }

    public function addParametr(Parametr $parametr):bool {
        if(!$this->parametry->contains($parametr)) {
            return $this->parametry->add($parametr);
        }
        return false;
    }

    public function removeParametr(Parametr $parametr):bool {
        return $this->parametry->removeElement($parametr);
    }


}