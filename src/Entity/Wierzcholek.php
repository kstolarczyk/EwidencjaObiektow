<?php


namespace App\Entity;


class Wierzcholek
{
    public float $x;

    public float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function serialize(): string
    {
        return "$this->x,$this->y;";
    }

    public static function unserialize(string $serialized): Wierzcholek
    {
        list($x, $y) = explode(',', $serialized, 2);
        return new Wierzcholek((float)$x, (float)$y);
    }

    public function czyWewnatrzObszaru(Obszar $obszar): bool
    {
        if ($this->x >= $obszar->getLewyGorny()->x && $this->x <= $obszar->getPrawyGorny()->x) {
            if ($this->y <= $obszar->getLewyGorny()->y && $this->y >= $obszar->getLewyDolny()->y) {
                return true;
            }
        }
        return false;
    }
}