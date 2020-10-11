<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametrRepository")
 * @ORM\Table(name="parametry")
 */
class Parametr implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private ?int $id = null;

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
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    private ?string $value = null;

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $executionContext)
    {
        $typ = $this->getTyp();
        if (!$typ instanceof TypParametru) return;
        $value = $this->getValue();
        switch ($typ->getTypDanych()) {
            case TypParametru::STRING:
                break;
            case TypParametru::INT:
                if ($value !== (string)(int)$value) {
                    $executionContext->buildViolation('The value should be an integer')
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::FLOAT:
                if ($value !== (string)(float)$value) {
                    $executionContext->buildViolation('The value should be float')
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::DATETIME:
                $errors = $executionContext->getValidator()->validate($value, [new Assert\DateTime(), new Assert\NotBlank()]);
                foreach ($errors as $error) {
                    /** @var ConstraintViolation $error */
                    $executionContext->buildViolation($error->getMessage())
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::DATE:
                $errors = $executionContext->getValidator()->validate($value, [new Assert\Date(), new Assert\NotBlank()]);
                foreach ($errors as $error) {
                    /** @var ConstraintViolation $error */
                    $executionContext->buildViolation($error->getMessage())
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::TIME:
                $errors = $executionContext->getValidator()->validate($value, [new Assert\Time(), new Assert\NotBlank()]);
                foreach ($errors as $error) {
                    /** @var ConstraintViolation $error */
                    $executionContext->buildViolation($error->getMessage())
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::ENUM:
                $acceptedValues = $typ->getAkceptowalneWartosci();
                if (!in_array($value, $acceptedValues)) {
                    $executionContext->buildViolation('Not acceptable value')
                        ->atPath('value')
                        ->addViolation();
                }
                break;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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

    public function jsonSerialize()
    {
        return [
            'parametrId' => $this->id,
            'wartosc' => $this->value,
            'obiektId' => $this->obiekt->getId(),
            'typParametrowId' => $this->typ->getId()
        ];
    }
}