<?PHP
namespace App\Service\Playoff\Grandfinal;

use App\Repository\SemifinalWinnersRepository;
use App\Service\Database\DatabaseServiceT;
use App\Entity\SemifinalWinners;
use App\Service\Playoff\PlayoffMatchesServiceInterface;

class GrandFinalMatchService implements PlayoffMatchesServiceInterface
{
    private DatabaseServiceT $databaseService;
    private SemifinalWinnersRepository $semifinalWinnersRepository;

    public function __construct(DatabaseServiceT $databaseService, SemifinalWinnersRepository $semifinalWinnersRepository)
    {
        $this->databaseService = $databaseService;
        $this->semifinalWinnersRepository = $semifinalWinnersRepository;
    }

    public function createMatches(string $stage): array
    {

        $semifinalWinners = $this->semifinalWinnersRepository->findAll();
        $possibleScores = ['3:0', '3:1', '3:2', '2:3', '1:3', '0:3'];


        if (count($semifinalWinners) < 2) {
            throw new \Exception('Not enough participants for the grand final');
        }


        shuffle($semifinalWinners);
        $selectedWinners = array_slice($semifinalWinners, 0, 2);

        $matches = [];
        $winners = [];
        $loosers = [];


        $team1 = $selectedWinners[0];
        $team2 = $selectedWinners[1];
        $score = $this->getRandomScore($possibleScores);
        list($score1, $score2) = explode(':', $score);

        $matches[] = [
            "Team1" => [
                'name' => $team1->getTeamName(),
                'score' => (int) $score1,
            ],
            "Team2" => [
                'name' => $team2->getTeamName(),
                'score' => (int) $score2,
            ]
        ];

        $result = $this->determineWinnerAndLoser((int) $score1, (int) $score2, $team1, $team2);
        $winner = $result['winner'];
        $looser = $result['looser'];

        $winners[] = $winner;
        $loosers[] = $looser;

        $this->databaseService->addPlayoffMatchesRes(
            'Grandfinal',
            $team1->getTeamName(),
            $team2->getTeamName(),
            (int) $score1,
            (int) $score2,
            'Group_0'
        );

        $this->databaseService->addPlayoffResults($winner, 1);
        $this->databaseService->addPlayoffResults($looser, 2);

        $winScore = 1000;
        $lossScore = 500;

        $winnerResult = $this->databaseService->getMatchResultByName($winner);
        $winRes = $winnerResult + $winScore;
        $this->databaseService->setMatchResultByName($winner, $winRes);

        $looserResult = $this->databaseService->getMatchResultByName($looser);
        $lossRes = $looserResult + $lossScore;
        $this->databaseService->setMatchResultByName($looser, $lossRes);

        return [
            'Grandfinal' => [
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
     * @param SemifinalWinners $team1
     * @param SemifinalWinners $team2
     * @return array
     */
    private function determineWinnerAndLoser(int $score1, int $score2, SemifinalWinners $team1, SemifinalWinners $team2): array
    {
        $winner = $score1 > $score2 ? $team1->getTeamName() : $team2->getTeamName();
        $looser = $score1 < $score2 ? $team1->getTeamName() : $team2->getTeamName();

        return ['winner' => $winner, 'looser' => $looser];
    }
}
