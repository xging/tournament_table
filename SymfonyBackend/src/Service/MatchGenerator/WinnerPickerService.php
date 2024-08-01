<?php
namespace App\Service\MatchGenerator;

use App\Repository\Teams\TeamsRepositoryInterface;
use App\Service\Interfaces\WinnerPickerServiceInterface;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;

class WinnerPicker implements WinnerPickerServiceInterface
{
    private TeamsRepositoryInterface $teamsRepository;
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;

    public function __construct(
        TeamsRepositoryInterface $teamsRepository,
        DatabaseServiceInterface $databaseService,
        LoggerInterface $logger
    ) {
        $this->teamsRepository = $teamsRepository;
        $this->databaseService = $databaseService;
        $this->logger = $logger;
    }

    public function pickWinners(array $teams, int $divisionId): array
    {
        $pickedFlag = $this->teamsRepository->pickedFlagCount(true);
        $numWinners = 4;

        $pickedWinners = $this->databaseService->getShortNameByPickedFlag(true, $divisionId);
        $this->logger->info('Picked Winners: ' . json_encode($pickedWinners));

        if (!$pickedFlag || empty($pickedWinners)) {
            $winnerTeams = array_rand(array_flip(array_column($teams, 'shortName')), $numWinners);
            foreach ($winnerTeams as $winner) {
                $this->databaseService->setPickedFlagByTeamShortName($winner, true);
                $this->logger->info('Winner Team: ' . json_encode($winner));
            }
            $this->logger->info('Winner Teams: ' . json_encode($winnerTeams));
        } else {
            $winnerTeams = $pickedWinners;
        }

        return $winnerTeams;
    }
}
