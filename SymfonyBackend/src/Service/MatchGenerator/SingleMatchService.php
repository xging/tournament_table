<?php
namespace App\Service\MatchGenerator;

use App\Repository\DivisionWinners\DivisionWinnersRepository;
use App\Repository\Teams\TeamsRepository;
use App\Service\Interfaces\SingleMatchInterface;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;
use App\Repository\Matches\MatchesRepositoryInterface;
use RuntimeException;
use App\Service\MatchGenerator\WinnerPicker;
use App\Service\MatchGenerator\MatchGenerator;

class SingleMatchService implements SingleMatchInterface
{
    private MatchesRepositoryInterface $matchesRepository;
    private TeamsRepository $teamsRepository;
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;
    private MatchGenerator $matchGenerator;
    private WinnerPicker $winnerPicker;
    private DivisionWinnersRepository $divisionWinnersRepository;

    public function __construct(
        MatchesRepositoryInterface $matchesRepository,
        DatabaseServiceInterface $databaseService,
        TeamsRepository $teamsRepository,
        LoggerInterface $logger,
        MatchGenerator $matchGenerator,
        WinnerPicker $winnerPicker,
        DivisionWinnersRepository $divisionWinnersRepository
    ) {
        $this->matchesRepository = $matchesRepository;
        $this->databaseService = $databaseService;
        $this->logger = $logger;
        $this->teamsRepository = $teamsRepository;
        $this->matchGenerator = $matchGenerator;
        $this->winnerPicker = $winnerPicker;
        $this->divisionWinnersRepository = $divisionWinnersRepository;
    }

    public function createMatches(array $teams, string $divisionName, int $divisionId): array
    {
        if (!isset($teams[$divisionName])) {
            throw new RuntimeException("Division {$divisionName} not found in teams data.");
        }
        $teamsA = $teams[$divisionName]['teams'];
        $this->logger->info("Teams AA: " . json_encode($teamsA, JSON_PRETTY_PRINT));
        $wins = array_fill_keys(array_column($teamsA, 'shortName'), 0);
        $maxWins = 7;

        $winnerTeams = $this->winnerPicker->pickWinners($teamsA, $divisionId);
        $matches = [];
        $ind = $this->databaseService->getIndexId($divisionName);
        if($ind === 7) {
            $this->databaseService->setIndexId(0,$divisionName);
            $ind = $this->databaseService->getIndexId($divisionName);
        }

        $this->logger->info("Team Index: $ind");
        // $this->logger->info("Teams Count: count($teamsA)");

        foreach ($teamsA as $i => $team1) {
            for ($j = $i + 1; $j < count($teamsA); $j++) {
                $team2 = $teamsA[$j];

                $team1Id = $this->databaseService->getTeamIdByShortName($team1['shortName']);
                $team2Id = $this->databaseService->getTeamIdByShortName($team2['shortName']);

                if ($team1Id === null || $team2Id === null) {
                    $this->logger->error("Failed to find team IDs for {$team1['shortName']} or {$team2['shortName']}");
                    continue;
                }

                if ($this->matchesRepository->matchExists($team1Id, $team2Id)) {
                    $this->logger->info("Match already exists between {$team1['shortName']} and {$team2['shortName']}");
                    continue;
                }

                $this->logger->info("Adding match between {$team1['shortName']} and {$team2['shortName']}");
                $score = $this->matchGenerator->generateMatchScore($team1, $team2, $winnerTeams, $wins, $maxWins);
                $matches[$team1['shortName']][$team2['shortName']] = $score;
                $matches[$team2['shortName']][$team1['shortName']] = implode(':', array_reverse(explode(':', $score)));

                $this->databaseService->addMatch($divisionId, $team1Id, $team2Id, $score);
                $this->databaseService->addMatch($divisionId, $team2Id, $team1Id, implode(':', array_reverse(explode(':', $score))));
                break;
            }
            $indexTemp = $i + 1;
            $this->databaseService->setIndexId($indexTemp,$divisionName);
            if ($i >= $ind) {
                break;
            }
        }

        foreach ($wins as $teamName => $count) {
            $teamResult = $this->databaseService->getMatchResult($teamName);
            $teamRes = $count + $teamResult;
            $this->databaseService->setMatchResult($teamName, $teamRes);
           

            $teamID = $this->databaseService->getTeamIdByShortName($teamName);
            if(($this->databaseService->getDWResultById($teamID))) {
                $this->databaseService->setDWResultById($teamID, $teamRes);
            }
           
        }

        arsort($wins);
        $this->logger->info('Winner teams:', $wins);
        $topTeams = array_slice(array_keys($wins), 0, 4);
        $this->logger->info('Top teams:', $topTeams);

        // $topTeamsRes = $this->databaseService->getTeamByShortName()
        
       
        foreach ($topTeams as $teamName) {
        
            

            $teamId = $this->databaseService->getTeamIdByShortName($teamName);
            $teamFullName = $this->databaseService->getTeamByShortName($teamName);
            $teamIsPickedForWin = $this->databaseService->getPickedFlagByShortName($teamName);



            // if ($teamId !== null && $teamIsPickedForWin) {
            //     $this->databaseService->addDivisionWinners($divisionId, $teamId, $teamFullName, $wins[$teamName], false);
            // }



            $this->logger->info("Win res: $teamName $wins[$teamName]");


        }


         //Add Division Winners

         $divisionWinnersList = $this->databaseService->getDWByDivisionId($divisionId);
         $this->logger->info("Initial division winners: " . json_encode($divisionWinnersList));

         if (empty($divisionWinnersList)) {
             $divisionsWinners = $this->databaseService->getTeamByPickedFlag(true);
        
             foreach ($divisionsWinners as $index => $divisionsWinner) {
                 $this->databaseService->addDivisionWinners(
                     $divisionId,
                     $divisionsWinner->getTeamID(),
                     $divisionsWinner->getName(),
                     0,
                     false
                 );
                $this->logger->info("Team: ".$divisionsWinner->getName());
                $this->logger->info("Division winners here.");
                
                if($index === 3) {
                    break;
                }
             }
         
             $this->logger->info("Division winners added successfully.");
         } else {
             $this->logger->info("Division winners table is full!");
         }

        return ['matches' => $matches, 'wins' => $wins, 'topTeams' => $topTeams];
    }
}