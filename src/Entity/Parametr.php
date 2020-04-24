<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypParametru")
     * @ORM\JoinColumn(name="typ_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private ?TypParametru $typ = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Obiekt", inversedBy="parametry")
     * @ORM\JoinColumn(name="obiekt_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private ?Obiekt $obiekt = null;

    /**
     * @ORM\Column(name="value", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private ?string $value = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTyp(): ?TypParametru
    {
        return $this->typ;
    }

    public function setTyp(?TypParametru $typ): void
    {
        $this->typ = $typ;
    }

    public function getObiekt(): ?Obiekt
    {
        return $this->obiekt;
    }

    public function setObiekt(?Obiekt $obiekt): void
    {
        $this->obiekt = $obiekt;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

}