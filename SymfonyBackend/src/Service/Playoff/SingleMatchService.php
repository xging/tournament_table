<?php

namespace App\Service\Playoff;

use App\Entity\Divisions;
use App\Entity\QuarterfinalWinners;
use App\Repository\DivisionWinners\DivisionWinnersRepository;
use App\Repository\QuarterfinalWinnersRepository;
use App\Repository\SemifinalLoosersRepository;
use App\Entity\DivisionWinners;
use App\Service\Database\DatabaseServiceT;
use App\Service\Playoff\PFSingleMatchesServiceInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Interfaces\TeamInterface;
use App\Repository\SemifinalWinnersRepository;

use Exception;

class SingleMatchService implements PFSingleMatchesServiceInterface
{
    private DatabaseServiceT $databaseService;
    private DivisionWinnersRepository $divisionWinnersRepository;
    private QuarterfinalWinnersRepository $quarterfinalWinnersRepository;
    private SemifinalLoosersRepository $semifinalLoosersRepository;
    private SemifinalWinnersRepository $semifinalWinnersRepository;
    private LoggerInterface $logger;

    public function __construct(
        DatabaseServiceT $databaseService,
        DivisionWinnersRepository $divisionWinnersRepository,
        QuarterfinalWinnersRepository $quarterfinalWinnersRepository,
        SemifinalLoosersRepository $semifinalLoosersRepository,
        SemifinalWinnersRepository $semifinalWinnersRepository,
        LoggerInterface $logger
    ) {
        $this->databaseService = $databaseService;
        $this->divisionWinnersRepository = $divisionWinnersRepository;
        $this->quarterfinalWinnersRepository = $quarterfinalWinnersRepository;
        $this->semifinalLoosersRepository = $semifinalLoosersRepository;
        $this->semifinalWinnersRepository = $semifinalWinnersRepository;
        $this->logger = $logger;
    }

    public function createMatches(string $stage, string $stage_2, string $stage_3, string $stage_4, int $matchesCount): array
    {

        $this->logger->info("Playoff Matches Count: $matchesCount");

        if ($matchesCount < 4) {
            $ind = $this->databaseService->getIndexId($stage);
            $stageStep = 0;
            $quarterfinaldata = $this->generateMatch($ind, $stage, $stageStep);

            return [
                'QuarterFinal' => $quarterfinaldata
            ];
        }
        if ($matchesCount >= 4 && $matchesCount < 6) {
            $ind = $this->databaseService->getIndexId($stage_2);
            $stageStep = 1;
            $semiFinaldata = $this->generateMatch($ind, $stage_2, $stageStep);

            return [
                'SemiFinal' => $semiFinaldata
            ];
        }
        if ($matchesCount >= 6 && $matchesCount < 7) {
            $stageStep = 2;
            $bronzeMedaldata = $this->generateMatch(0, $stage_3, $stageStep);
            return [
                'BronzeMedal' => $bronzeMedaldata
            ];

        }

        if ($matchesCount >= 7) {
            $stageStep = 3;
            $grandFinaldata = $this->generateMatch(0, $stage_4, $stageStep);
            return [
                'Grandfinal' => $grandFinaldata
            ];

        } else
            return [];
    }

    private function getRandomScore(array $possibleScores): string
    {
        return $possibleScores[array_rand($possibleScores)];
    }

    private function determineWinnerAndLoser(int $score1, int $score2, TeamInterface $team1, TeamInterface $team2, int $stageStep): array
    {
        $winner = $score1 > $score2 ? $team1->getTeamName() : $team2->getTeamName();
        $looser = $score1 < $score2 ? $team1->getTeamName() : $team2->getTeamName();


        if ($stageStep === 0) {
            $winScore = 50;
            $lossScore = 25;
        } else if ($stageStep === 1) {
            $winScore = 100;
            $lossScore = 50;
        } else if ($stageStep === 2) {
            $winScore = 250;
            $lossScore = 100;
        } else if ($stageStep === 3) {
            $winScore = 1000;
            $lossScore = 500;
        }

        $winnerResult = $this->databaseService->getMatchResultByName($winner);
        $winRes = $winnerResult + $winScore;
        $this->databaseService->setMatchResultByName($winner, $winRes);

        $looserResult = $this->databaseService->getMatchResultByName($looser);
        $lossRes = $looserResult + $lossScore;
        $this->databaseService->setMatchResultByName($looser, $lossRes);

        return ['winner' => $winner, 'looser' => $looser];
    }


    private function generateMatch(int $ind, string $stage, int $stageStep): array
    {
        $stagesArr = ['Quarterfinal', 'Semifinal', 'BronzeMedal', 'Grandfinal'];
        $quarterListParticipants = [];

        if ($stagesArr[0] === $stage) {
            $quarterListParticipants = $this->divisionWinnersRepository->findAll();
        } else if ($stagesArr[1] === $stage) {
            $this->logger->info('Semifinal tut 1');
            $quarterListParticipants = $this->quarterfinalWinnersRepository->findAll();
        } else if ($stagesArr[2] === $stage) {
            $quarterListParticipants = $this->semifinalLoosersRepository->findAll();
        } else if ($stagesArr[3] === $stage) {
            $quarterListParticipants = $this->semifinalWinnersRepository->findAll();
        }


        $filteredParticipants = array_filter($quarterListParticipants, function ($participant) {
            return !$participant->isPickedFlag();
        });

        if (count($filteredParticipants) < 2) {
            throw new Exception('Not enough participants to generate a match.');
        }

        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];
        shuffle($filteredParticipants);

        $matches = [];
        $winners = [];
        $loosers = [];
        $place = 5;

        $selectedWinners = array_slice($filteredParticipants, 0, 2);
        $score = $this->getRandomScore($possibleScores);
        list($score1, $score2) = explode(':', $score);

        if ($ind === 4) {
            $this->databaseService->setIndexId(0, $stage);
            $ind = $this->databaseService->getIndexId($stage);
        }

        $team1 = $selectedWinners[0];
        $team2 = $selectedWinners[1];

        if ($stagesArr[0] === $stage) {
            $this->databaseService->setDWPickedFlagByName($team1->getTeamName(), true);
            $this->databaseService->setDWPickedFlagByName($team2->getTeamName(), true);
        } else if ($stagesArr[1] === $stage) {
            $this->databaseService->setQWPickedFlagByName($team1->getTeamName(), true);
            $this->databaseService->setQWPickedFlagByName($team2->getTeamName(), true);
        } else if ($stagesArr[2] === $stage) {
            $this->databaseService->setSLPickedFlagByName($team1->getTeamName(), true);
            $this->databaseService->setSLPickedFlagByName($team2->getTeamName(), true);
        } else if ($stagesArr[3] === $stage) {
            $this->databaseService->setSWPickedFlagByName($team1->getTeamName(), true);
            $this->databaseService->setSWPickedFlagByName($team2->getTeamName(), true);
        }

        $matches[] = [
            "Team" . ($ind * 2 + 1) => [
                'name' => $team1->getTeamName(),
                'score' => (int) $score1,
            ],
            "Team" . ($ind * 2 + 2) => [
                'name' => $team2->getTeamName(),
                'score' => (int) $score2,
            ]
        ];

        $result = $this->determineWinnerAndLoser((int) $score1, (int) $score2, $team1, $team2, (int) $stageStep);

        $winner = $result['winner'];
        $looser = $result['looser'];

        $winners[] = $winner;
        $loosers[] = $looser;

        $this->databaseService->addPlayoffMatchesRes(
            $stage,
            $team1->getTeamName(),
            $team2->getTeamName(),
            (int) $score1,
            (int) $score2,
            'Group_' . $ind
        );

        if ($stagesArr[0] === $stage) {
            $this->databaseService->addQuarterfinalWinners($winner, false);
            $this->databaseService->addPlayoffResults($looser, $place++);
        } else if ($stagesArr[1] === $stage) {
            $this->databaseService->addSemifinalWinners($winner, false);
            $this->databaseService->addSemifinalLoosers($looser, false);
        }
        if ($stagesArr[0] === $stage || $stagesArr[1]) {
            $this->databaseService->setIndexId($ind + 1, $stage);
        }
        return [
            'Matches' => $matches,
            'Winners' => $winners,
            'Loosers' => $loosers
        ];
    }
}