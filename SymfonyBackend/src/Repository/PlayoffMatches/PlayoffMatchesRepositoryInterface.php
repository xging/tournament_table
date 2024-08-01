<?php

namespace App\Repository\PlayoffMatches;

use App\Entity\PlayoffMatch;

interface PlayoffMatchesRepositoryInterface
{
    public function findAll(): array;
    public function findBy(array $criteria): array;
}