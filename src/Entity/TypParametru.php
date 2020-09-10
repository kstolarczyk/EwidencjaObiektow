<?php


namespace App\Entity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypParametruRepository")
 * @ORM\Table(name="typy_parametrow")
 */
class TypParametru implements \JsonSerializable
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
     * @ORM\Column(name="typ_danych", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $typDanych = null;

    /**
     * @ORM\Column(name="jednostka_miary", type="string", nullable=true)
     */
    private ?string $jednostkaMiary = null;

    /**
     * @ORM\Column(name="akceptowalne_wartosci", type="array", nullable=true)
     */
    private ?array $akceptowalneWartosci = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\GrupaObiektow", mappedBy="typyParametrow")
     */
    private Collection $grupyObiektow;

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

    public function getTypDanych(): ?string
    {
        return $this->typDanych;
    }

    public function setTypDanych(?string $typDanych): void
    {
        $this->typDanych = $typDanych;
    }

    public function getJednostkaMiary(): ?string
    {
        return $this->jednostkaMiary;
    }

    public function setJednostkaMiary(?string $jednostkaMiary): void
    {
        $this->jednostkaMiary = $jednostkaMiary;
    }

    public function getAkceptowalneWartosci(): ?array
    {
        return $this->akceptowalneWartosci;
    }

    public function setAkceptowalneWartosci(?array $akceptowalneWartosci): void
    {
        $this->akceptowalneWartosci = $akceptowalneWartosci;
    }

    public function getGrupyObiektow(): Collection
    {
        return $this->grupyObiektow;
    }

    public function setGrupyObiektow(Collection $grupyObiektow): void
    {
        $this->grupyObiektow = $grupyObiektow;
    }


    public const DATE = "DATE";
    public const DATETIME = "DATETIME";
    public const TIME = "TIME";
    public const INT = "INT";
    public const FLOAT = "FLOAT";
    public const STRING = "STRING";
    public const ENUM = "ENUM";

    public static function getTypyDanych(): array
    {
        return [
            static::INT,
            static::FLOAT,
            static::STRING,
            static::DATE,
            static::TIME,
            static::DATETIME,
            static::ENUM
        ];
    }

    public function jsonSerialize()
    {
        return [
            'typParametrowId' => $this->id,
            'nazwa' => $this->nazwa,
            'symbol' => $this->symbol,
            'jednostkaMiary' => $this->jednostkaMiary,
            'typDanych' => $this->typDanych,
            'akceptowalneWartosci' => $this->akceptowalneWartosci
        ];
    }
}