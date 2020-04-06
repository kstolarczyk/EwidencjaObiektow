<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObszarRepository")
 * @ORM\Table(name="obszary")
 */
class Obszar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private int $id;

    /**
     * @ORM\Column(name="symbol", type="string", nullable=false)
     */
    private string $symbol;

    /**
     * @ORM\Column(name="nazwa", type="string", nullable=false)
     */
    private string $nazwa;

    /**
     * @ORM\Column(name="wierzcholki_serialized", type="string", nullable=false)
     */
    private string $wierzcholkiSerialized;

    private Wierzcholek $lewyGorny;
    private Wierzcholek $prawyGorny;
    private Wierzcholek $lewyDolny;
    private Wierzcholek $prawyDolny;

    private Wierzcholek $srodek;

    public function __unserialize(array $data): void
    {
        foreach ($data as $property => $value) {
            $propertyName = str_replace(static::class, "", $property);
            $propertyName = preg_replace('/[^\w]/', '', $propertyName);
            $this->{$propertyName} = $value;
            if ($propertyName === 'wierzcholkiSerialized') {
                list($w1, $w2, $w3, $w4) = explode(';', $value, 4);
                $this->lewyGorny = Wierzcholek::unserialize($w1);
                $this->prawyGorny = Wierzcholek::unserialize($w2);
                $this->lewyDolny = Wierzcholek::unserialize($w3);
                $this->prawyDolny = Wierzcholek::unserialize($w4);
            }
        }
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

    public function getSrodek(): Wierzcholek
    {
        return $this->srodek;
    }

    public function setSrodek(Wierzcholek $srodek, float $dlugoscBoku)
    {
        $this->srodek = $srodek;
        $przesuniecie = $dlugoscBoku / 2.0;
        $this->lewyGorny = new Wierzcholek($srodek->x - $przesuniecie, $srodek->y + $przesuniecie);
        $this->prawyGorny = new Wierzcholek($srodek->x + $przesuniecie, $srodek->y + $przesuniecie);
        $this->lewyDolny = new Wierzcholek($srodek->x - $przesuniecie, $srodek->y - $przesuniecie);
        $this->prawyDolny = new Wierzcholek($srodek->x + $przesuniecie, $srodek->y - $przesuniecie);
        $this->wierzcholkiSerialized = '';
        $this->wierzcholkiSerialized .= $this->lewyGorny->serialize();
        $this->wierzcholkiSerialized .= $this->prawyGorny->serialize();
        $this->wierzcholkiSerialized .= $this->lewyDolny->serialize();
        $this->wierzcholkiSerialized .= $this->prawyDolny->serialize();
    }

    public function czyPrzeslaniaInnyObszar(Obszar $innyObszar): bool
    {
        if ($this->lewyGorny->czyWewnatrzObszaru($innyObszar)) return true;
        if ($this->prawyGorny->czyWewnatrzObszaru($innyObszar)) return true;
        if ($this->lewyDolny->czyWewnatrzObszaru($innyObszar)) return true;
        if ($this->prawyDolny->czyWewnatrzObszaru($innyObszar)) return true;
        return false;
    }

    public function getLewyGorny(): Wierzcholek
    {
        return $this->lewyGorny;
    }

    public function getPrawyGorny(): Wierzcholek
    {
        return $this->prawyGorny;
    }

    public function getLewyDolny(): Wierzcholek
    {
        return $this->lewyDolny;
    }

    public function getPrawyDolny(): Wierzcholek
    {
        return $this->prawyDolny;
    }

}