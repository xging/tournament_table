<?PHP
namespace App\Service\Playoff\Semifinal;

use App\Repository\QuarterfinalWinnersRepository;
use App\Entity\QuarterfinalWinners;
use App\Service\Database\DatabaseServiceT;
use App\Service\Playoff\PlayoffMatchesServiceInterface;
use Exception;
class SemiFinalMatchService implements PlayoffMatchesServiceInterface
{
    private DatabaseServiceT $databaseService;
    private QuarterfinalWinnersRepository $quarterfinalWinnersRepository;


    public function __construct(DatabaseServiceT $databaseService, QuarterfinalWinnersRepository $quarterfinalWinnersRepository)
    {
        $this->databaseService = $databaseService;
        $this->quarterfinalWinnersRepository = $quarterfinalWinnersRepository;
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
        $quarterListParticipants = $this->quarterfinalWinnersRepository->findAll();
        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];

        if (count($quarterListParticipants) < 4) {
            throw new Exception('Not enough participants for the semi-finals');
        }

        shuffle($quarterListParticipants);
        $selectedWinners = array_slice($quarterListParticipants, 0, 4);

        $matches = [];
        $winners = [];
        $loosers = [];

        for ($i = 0; $i < 2; $i++) {
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
                'Semifinal',
                $team1->getTeamName(),
                $team2->getTeamName(),
                (int)$score1,
                (int)$score2,
                'Group_' . $i
            );

            $this->databaseService->addSemifinalWinners($winner, false);
            $this->databaseService->addSemifinalLoosers($looser, false);
        
            $winScore = 100;
            $lossScore = 50;

            $winnerResult = $this->databaseService->getMatchResultByName($winner);
            $winRes = $winnerResult+$winScore;
            $this->databaseService->setMatchResultByName($winner, $winRes);
    
            $looserResult = $this->databaseService->getMatchResultByName($looser);
            $lossRes = $looserResult+$lossScore;
            $this->databaseService->setMatchResultByName($looser, $lossRes);
        }
       
        return [
            'SemiFinal' => [
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
     * @param QuarterfinalWinners $team1
     * @param QuarterfinalWinners $team2
     * @return array
     */
    private function determineWinnerAndLoser(int $score1, int $score2, QuarterfinalWinners $team1, QuarterfinalWinners $team2): array
    {
        $winner = $score1 > $score2 ? $team1->getTeamName() : $team2->getTeamName();
        $looser = $score1 < $score2 ? $team1->getTeamName() : $team2->getTeamName();

        return ['winner' => $winner, 'looser' => $looser];
    }

}
