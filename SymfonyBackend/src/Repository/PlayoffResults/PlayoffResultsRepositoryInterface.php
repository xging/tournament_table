<?php

namespace App\Repository\PlayoffResults;

use App\Entity\PlayoffMatch;

interface PlayoffResultsRepositoryInterface
{
    public function findAll(): array;
    public function findBy(array $criteria): array;
}