<?php

namespace App\Repository\Matches;

use App\Entity\Divisions;
use App\Entity\Teams;

interface MatchesRepositoryInterface
{
    public function findBy(array $criteria): array;
    public function matchExists(int $team1Id, int $team2Id): bool;
}