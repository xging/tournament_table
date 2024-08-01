<?php

namespace App\Service\Playoff\Bronze;

use App\Repository\PlayoffMatches\PlayoffMatchesRepository;
use App\Repository\PlayoffMatches\PlayoffMatchesRepositoryInterface;
use App\Service\Playoff\PlayoffDataMatchesServiceInterface;

class BronzeMedalDataService implements PlayoffDataMatchesServiceInterface
{
    private PlayoffMatchesRepositoryInterface $playoffMatchesRepository;

    public function __construct(PlayoffMatchesRepositoryInterface $playoffMatchesRepository)
    {
        $this->playoffMatchesRepository = $playoffMatchesRepository;
    }

    public function getMatchData($groupStageName): array
    {
        $playoffMatches = $this->playoffMatchesRepository->findAll();

        if (empty($playoffMatches)) {
            throw new \Exception('No playoff matches found.');
        }

        $data = [];

        foreach ($playoffMatches as $playoffMatch) {


            $groupStageLoop = $this->playoffMatchesRepository->findBy(['group_stage' => $groupStageName]);

            foreach ($groupStageLoop as $groupLoop) {
                $groupName = $groupLoop->getGroupName();
                $teamName1 = $groupLoop->getTeamName1();
                $teamName2 = $groupLoop->getTeamName2();
                $teamScore1 = $groupLoop->getTeamScore1();
                $teamScore2 = $groupLoop->getTeamScore2();
                if (!isset($data[$groupName])) {
                    $data[$groupName] = [];
                }
                $data[$groupName][$teamName1] = $teamScore1;
                $data[$groupName][$teamName2] = $teamScore2;
            }
        }

        return [$groupStageName => $data];
    }
}
