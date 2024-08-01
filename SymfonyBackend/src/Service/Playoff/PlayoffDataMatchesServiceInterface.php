<?php

namespace App\Service\Playoff;

interface PlayoffDataMatchesServiceInterface
{
    public function getMatchData(string $groupStageName): array;
}