<?php


namespace App\Form\DataTransformer;


use App\Entity\TypParametru;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

class TypParametruTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function transform($value)
    {
        if ($value instanceof TypParametru) {
            return $value->getId();
        }
        return null;

    }

    public function reverseTransform($value)
    {
        $id = (int)$value;
        return $id > 0 ? $this->entityManager->getRepository(TypParametru::class)->find($id) : null;
    }
}