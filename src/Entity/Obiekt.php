<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
    private ?int $id = null;

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

    /**
     * @ORM\Column(name="zdjecie", type="string", nullable=true)
     */
    private ?string $zdjecie = null;

    /**
     * @Assert\Image()
     */
    private ?UploadedFile $imgFile = null;

    public function __construct()
    {
        $this->parametry = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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
        return [
          'obiektId' => $this->id,
          'grupaObiektowId' => $this->grupa->getId(),
          'nazwa' => $this->nazwa,
          'symbol' => $this->symbol,
          'longitude' => $this->dlugosc,
          'latitude' => $this->szerokosc,
          'parametry' => $this->parametry->getValues()
        ];
    }

    public function getZdjecie(): ?string
    {
        return $this->zdjecie;
    }

    public function setZdjecie(?string $zdjecie): void
    {
        $this->zdjecie = $zdjecie;
    }

    public function getImgFile(): ?UploadedFile
    {
        return $this->imgFile;
    }

    public function setImgFile(?UploadedFile $imgFile): void
    {
        $this->imgFile = $imgFile;
    }

    public function setPlainData($key, $value)
    {
        try {
            $this->{$key} = $value;
        } catch (\Exception $e) {
        }
    }

    /**
     * @Assert\Callback
     */
    public function valdiate(ExecutionContextInterface $context)
    {
        if ($this->grupa === null) return;
        if ($this->parametry->count() !== $this->grupa->getTypyParametrow()->count()) {
            $context->buildViolation("Liczba parametrów niezgodna z grupą obiektów")
                ->atPath("parametry")->addViolation();
            return;
        }
        foreach ($this->parametry as $i => $parametr) {
            if (!$parametr instanceof Parametr) continue;
            $typ = $parametr->getTyp();
            if ($typ !== null && $this->grupa->getTypyParametrow()->exists(fn(int $j, TypParametru $t) => $t->getId() === $typ->getId())) continue;
            $context->buildViolation("Nieprawidłowy typ parametru dla wybranej grupy obiektów")
                ->atPath("parametry[$i]")
                ->addViolation();
        }
    }
}