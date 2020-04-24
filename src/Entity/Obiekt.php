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
class Obiekt
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
     *  @Assert\NotBlank()
     */
    private ?string $nazwa ="";

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GrupaObiektow", inversedBy="obiekty")
     * @ORM\JoinColumn(name="grupa_id", referencedColumnName="id")
     */
    private ?GrupaObiektow $grupa = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Parametr", mappedBy="obiekt")
     */
    private Collection $parametry;

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