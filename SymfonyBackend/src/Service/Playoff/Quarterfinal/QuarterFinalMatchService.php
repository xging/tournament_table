<?PHP
namespace App\Service\Playoff\Quarterfinal;

use App\Repository\DivisionWinners\DivisionWinnersRepository;
use App\Entity\DivisionWinners;
use App\Service\Database\DatabaseServiceT;
use App\Service\Playoff\PlayoffMatchesServiceInterface;
use Exception;

class QuarterFinalMatchService implements PlayoffMatchesServiceInterface
{
    private DatabaseServiceT $databaseService;
    private DivisionWinnersRepository $divisionWinnersRepository;

    public function __construct(
        DatabaseServiceT $databaseService,
        DivisionWinnersRepository $divisionWinnersRepository
    ) {
        $this->databaseService = $databaseService;
        $this->divisionWinnersRepository = $divisionWinnersRepository;
    }


    /**
     *
     * @param array $teams
     * @param string $divisionName
     * @param int $divisionId
     * @return array
     * @throws Exception
     */

    public function createMatches(string $stage): array
    {
        $quarterListParticipants = $this->divisionWinnersRepository->findAll();
        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];

        if (count($quarterListParticipants) < 8) {
            throw new Exception('Not enough participants for the quarter-finals');
        }

        shuffle($quarterListParticipants);
        $selectedWinners = array_slice($quarterListParticipants, 0, 8);

        $matches = [];
        $winners = [];
        $loosers = [];
        $place = 5;

        for ($i = 0; $i < 4; $i++) {
            $team1 = $selectedWinners[$i * 2];
            $team2 = $selectedWinners[$i * 2 + 1];
            $score = $this->getRandomScore($possibleScores);
            list($score1, $score2) = explode(':', $score);

            $matches[] = [
                "Team" . ($i * 2 + 1) => [
                    'name' => $team1->getTeamName(),
                    'score' => (int)$score1,
                ],
                "Team" . ($i * 2 + 2) => [
                    'name' => $team2->getTeamName(),
                    'score' => (int)$score2,
                ]
            ];

            $result = $this->determineWinnerAndLoser((int)$score1, (int)$score2, $team1, $team2);
            $winner = $result['winner'];
            $looser = $result['looser'];

            $winners[] = $winner;
            $loosers[] = $looser;

            $this->databaseService->addPlayoffMatchesRes(
                'Quarterfinal',
                $team1->getTeamName(),
                $team2->getTeamName(),
                (int)$score1,
                (int)$score2,
                'Group_' . $i
            );

            $this->databaseService->addQuarterfinalWinners($winner,false);
            $this->databaseService->addPlayoffResults($looser, $place++);

            $winScore = 50;
            $lossScore = 25;

            $winnerResult = $this->databaseService->getMatchResultByName($winner);
            $winRes = $winnerResult+$winScore;
            $this->databaseService->setMatchResultByName($winner, $winRes);
    
            $looserResult = $this->databaseService->getMatchResultByName($looser);
            $lossRes = $looserResult+$lossScore;
            $this->databaseService->setMatchResultByName($looser, $lossRes);
        }
          

        return [
            'QuarterFinal' => [
                'Matches' => $matches,
                'Winners' => $winners,
                'Loosers' => $loosers
            ]
        ];
    }

    private function getRandomScore(array $possibleScores): string
    {
        return $possibleScores[array_rand($possibleScores)];
    }

      /**
     *
     * @param int $score1
     * @param int $score2
     * @param DivisionWinners $team1
     * @param DivisionWinners $team2
     * @return array
     */
    private function determineWinnerAndLoser(int $score1, int $score2, DivisionWinners $team1, DivisionWinners $team2): array
    {
        $winner = $score1 > $score2 ? $team1->getTeamName() : $team2->getTeamName();
        $looser = $score1 < $score2 ? $team1->getTeamName() : $team2->getTeamName();

        return ['winner' => $winner, 'looser' => $looser];
    }
}
