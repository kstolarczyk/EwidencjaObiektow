<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametrRepository")
 * @ORM\Table(name="parametry")
 */
class Parametr
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    public int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypParametru")
     * @ORM\JoinColumn(name="typ_id", referencedColumnName="id")
     */
    public TypParametru $typ;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Obiekt", inversedBy="parametry")
     * @ORM\JoinColumn(name="obiekt_id", referencedColumnName="id")
     */
    public Obiekt $obiekt;

    /**
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    public string $value;

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
     * @return TypParametru
     */
    public function getTyp(): TypParametru
    {
        return $this->typ;
    }

    /**
     * @param TypParametru $typ
     */
    public function setTyp(TypParametru $typ): void
    {
        $this->typ = $typ;
    }

    /**
     * @return Obiekt
     */
    public function getObiekt(): Obiekt
    {
        return $this->obiekt;
    }

    /**
     * @param Obiekt $obiekt
     */
    public function setObiekt(Obiekt $obiekt): void
    {
        $this->obiekt = $obiekt;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }


}