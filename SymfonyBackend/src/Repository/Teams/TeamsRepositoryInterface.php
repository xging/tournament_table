<?php

namespace App\Repository\Teams;

use App\Entity\Teams;

interface TeamsRepositoryInterface
{
    public function find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): ?Teams;
    public function findBy(array $criteria): array;
    public function findAll(): array;
    public function pickedFlagCount(bool $flag): bool;
}
