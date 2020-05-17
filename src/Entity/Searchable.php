<?php


namespace App\Entity;


interface Searchable
{
    public static function getSearchableProperties(): array;
}