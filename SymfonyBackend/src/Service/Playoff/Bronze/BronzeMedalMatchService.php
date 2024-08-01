<?PHP
namespace App\Service\Playoff\Bronze;

use App\Repository\SemifinalLoosersRepository;
use App\Service\Database\DatabaseServiceT;
use App\Entity\SemifinalLoosers;
use App\Service\Playoff\PlayoffMatchesServiceInterface;
use Psr\Log\LoggerInterface;

class BronzeMedalMatchService implements PlayoffMatchesServiceInterface
{
    private DatabaseServiceT $databaseService;
    private SemifinalLoosersRepository $semifinalLoosersRepository;
    private $logger;
    public function __construct(DatabaseServiceT $databaseService, SemifinalLoosersRepository $semifinalLoosersRepository, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->semifinalLoosersRepository = $semifinalLoosersRepository;
        $this->logger = $logger;
    }

    public function createMatches(string $stage): array
    {
        $semifinalLoosers = $this->semifinalLoosersRepository->findAll();
        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];

        if (count($semifinalLoosers) < 2) {
            throw new \Exception('Not enough participants for the bronze medal match');
        }

        shuffle($semifinalLoosers);
        $selectedLoosers = array_slice($semifinalLoosers, 0, 2);

        $matches = [];
        $winners = [];
        $loosers = [];

        $team1 = $selectedLoosers[0];
        $team2 = $selectedLoosers[1];
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
            'BronzeMedal',
            $team1->getTeamName(),
            $team2->getTeamName(),
            (int) $score1,
            (int) $score2,
            'Group_0'
        );

        $this->databaseService->addPlayoffResults($winner, 3);
        $this->databaseService->addPlayoffResults($looser, 4);

        $winScore = 250;
        $lossScore = 100;

        $winnerResult = $this->databaseService->getMatchResultByName($winner);
        $winRes = $winnerResult + $winScore;
        $this->databaseService->setMatchResultByName($winner, $winRes);

        $looserResult = $this->databaseService->getMatchResultByName($looser);
        $lossRes = $looserResult + $lossScore;
        $this->databaseService->setMatchResultByName($looser, $lossRes);
        return [
            'BronzeMedal' => [
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
     * @param SemifinalLoosers $team1
     * @param SemifinalLoosers $team2
     * @return array
     */
    private function determineWinnerAndLoser(int $score1, int $score2, SemifinalLoosers $team1, SemifinalLoosers $team2): array
    {
        $winner = $score1 > $score2 ? $team1->getTeamName() : $team2->getTeamName();
        $looser = $score1 < $score2 ? $team1->getTeamName() : $team2->getTeamName();

        return ['winner' => $winner, 'looser' => $looser];
    }
}
