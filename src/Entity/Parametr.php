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
     * @ORM\Column(name="value", type="object", nullable=true)
     */
    private $value = null;

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
                if (!is_int($value)) {
                    $executionContext->buildViolation('The value should be an integer')
                        ->atPath('value')
                        ->addViolation();
                }
                break;
            case TypParametru::FLOAT:
                if (!is_float($value)) {
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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): void
    {
        $this->value = $this->tryConvertToStrict($value);
    }

    public function jsonSerialize()
    {
        return [
            'parametrId' => $this->id,
            'wartosc' => $this->formattedValue(),
            'obiektId' => $this->obiekt->getId(),
            'typParametrowId' => $this->typ->getId()
        ];
    }

    private function formattedValue()
    {
        switch($this->typ->getTypDanych()) {
            case TypParametru::DATETIME:
                return $this->value->format('d.m.Y H:i');
            case TypParametru::DATE:
                return $this->value->format('d.m.Y');
            case TypParametru::TIME:
                return $this->value->format('H:i');
            default:
                return $this->value;
        }
    }

    private function tryConvertToStrict($value)
    {
        if($this->typ == null) return $value;
        switch($this->typ->getTypDanych()) {
            case TypParametru::DATETIME:
                if($value instanceof \DateTime) $value = $value->format("Y-m-d H:i");
                $tmp = \DateTime::createFromFormat("Y-m-d H:i", $value);
                if($tmp === false) $tmp = \DateTime::createFromFormat("d.m.Y H:i", $value);
                return $tmp;
            case TypParametru::DATE:
                if($value instanceof \DateTime) $value = $value->format("Y-m-d");
                $tmp = \DateTime::createFromFormat("Y-m-d", $value);
                if($tmp === false) $tmp = \DateTime::createFromFormat("d.m.Y", $value);
                return $tmp;
            case TypParametru::TIME:
                if($value instanceof \DateTime) $value = $value->format("H:i");
                return \DateTime::createFromFormat("H:i", $value);
            case TypParametru::FLOAT:
                if(is_float($value) || (string)(float) $value == $value) {
                    return (float) $value;
                }
                return $value;
            case TypParametru::INT:
                if(is_int($value) || (string)(int) $value == $value) {
                    return (int) $value;
                }
                return $value;
            default:
                return $value;
        }
    }
}