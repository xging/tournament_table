<?php

namespace App\Service\Playoff\Results;

use App\Repository\PlayoffResults\PlayoffResultsRepository;
use App\Service\Playoff\PlayoffDataResultsServiceInterface;
use App\Repository\Teams\TeamsRepository;

class BracketResultsDataService implements PlayoffDataResultsServiceInterface
{
    private $playoffResultsRepository;
    private $teamsRepository;

    public function __construct(PlayoffResultsRepository $playoffResultsRepository, TeamsRepository $teamsRepository)
    {
        $this->playoffResultsRepository = $playoffResultsRepository;
        $this->teamsRepository = $teamsRepository;
    }

    public function getMatchResults(): array
    {
        $teamsRepository = $this->teamsRepository->findAll();

        if (empty($teamsRepository)) {
            throw new \Exception('No playoff matches found.');
        }
        $data = [];

        foreach ($teamsRepository as $teamsRepositor) {
            $teamName = $teamsRepositor->getName();
            $teamPlace = $teamsRepositor->getResult();
            $data[$teamName] = (int) $teamPlace;
        }
        arsort($data);

        return ['Results' => $data];
    }
}
