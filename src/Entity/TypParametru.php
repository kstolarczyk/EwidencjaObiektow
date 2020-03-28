<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypParametruRepository")
 * @ORM\Table(name="typy_parametrow")
 */
class TypParametru
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
     * @ORM\Column(name="typ_danych", type="string", nullable=false)
     */
    public string $typDanych;

    /**
     * @ORM\Column(name="jednostka_miary", type="string", nullable=false)
     */
    public string $jednostkaMiary;

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

    public function getTypDanych(): string
    {
        return $this->typDanych;
    }

    public function setTypDanych(string $typDanych): void
    {
        $this->typDanych = $typDanych;
    }

    public function getJednostkaMiary(): string
    {
        return $this->jednostkaMiary;
    }

    public function setJednostkaMiary(string $jednostkaMiary): void
    {
        $this->jednostkaMiary = $jednostkaMiary;
    }


}