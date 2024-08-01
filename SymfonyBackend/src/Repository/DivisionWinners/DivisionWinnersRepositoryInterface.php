<?php

namespace App\Repository\DivisionWinners;

use App\Entity\Divisions;
use App\Entity\Teams;

interface DivisionWinnersRepositoryInterface
{
    public function findBy(array $criteria): array;
}