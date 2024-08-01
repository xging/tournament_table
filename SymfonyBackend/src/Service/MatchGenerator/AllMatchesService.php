<?php
namespace App\Service\MatchGenerator;

use App\Service\Interfaces\AllMatchesServiceInterface;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\MatchGenerator\MatchGenerator;
use App\Service\MatchGenerator\WinnerPicker;
use RuntimeException;

class AllMatchesService implements AllMatchesServiceInterface
{
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;
    private MatchGenerator $matchGenerator;
    private WinnerPicker $winnerPicker;

    public function __construct(
        DatabaseServiceInterface $databaseService,
        LoggerInterface $logger,
        MatchGenerator $matchGenerator,
        WinnerPicker $winnerPicker
    ) {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
        $this->matchGenerator = $matchGenerator;
        $this->winnerPicker = $winnerPicker;
    }

    /**
     * @param array $teams
     * @param string $divisionName
     * @param int $divisionId
     * @return array
     * @throws RuntimeException
     */
    public function createMatches(array $teams, string $divisionName, int $divisionId): array
    {
        if (!isset($teams[$divisionName])) {
            throw new RuntimeException("Division {$divisionName} not found in teams data.");
        }

        $teamsA = $teams[$divisionName]['teams'];
        $wins = array_fill_keys(array_column($teamsA, 'shortName'), 0);
        $maxWins = 7;
        $numWinners = 4;

        $winnerTeams = $this->winnerPicker->pickWinners($teamsA, $divisionId);

        $matches = [];
        foreach ($teamsA as $i => $team1) {
            for ($j = $i + 1; $j < count($teamsA); $j++) {
                $team2 = $teamsA[$j];

                $team1Id = $this->databaseService->getTeamIdByShortName($team1['shortName']);
                $team2Id = $this->databaseService->getTeamIdByShortName($team2['shortName']);

                if ($team1Id === null || $team2Id === null) {
                    $this->logger->error("Failed to find team IDs for {$team1['shortName']} or {$team2['shortName']}");
                    continue;
                }

                $this->logger->info("Adding match between {$team1['shortName']} and {$team2['shortName']}");
                $score = $this->matchGenerator->generateMatchScore($team1, $team2, $winnerTeams, $wins, $maxWins);

                $matches[$team1['shortName']][$team2['shortName']] = $score;
                $matches[$team2['shortName']][$team1['shortName']] = implode(':', array_reverse(explode(':', $score)));

                $this->databaseService->addMatch($divisionId, $team1Id, $team2Id, $score);
                $this->databaseService->addMatch($divisionId, $team2Id, $team1Id, implode(':', array_reverse(explode(':', $score))));
            }
        }

        foreach ($wins as $teamName => $count) {
            $teamResult = $this->databaseService->getMatchResult($teamName);
            $teamRes = $count + $teamResult;
            $this->databaseService->setMatchResult($teamName, $teamRes);
        }

        arsort($wins);
        $topTeams = array_slice(array_keys($wins), 0, $numWinners);
        foreach ($topTeams as $teamName) {
            $teamId = $this->databaseService->getTeamIdByShortName($teamName);
            $teamFullName = $this->databaseService->getTeamByShortName($teamName);
            if ($teamId !== null) {
                $this->databaseService->addDivisionWinners($divisionId, $teamId, $teamFullName, $wins[$teamName], false);
            }
        }

        $this->logger->debug("Top Teams: " . json_encode($topTeams));

        return ['matches' => $matches, 'wins' => $wins, 'topTeams' => $topTeams];
    }

}
