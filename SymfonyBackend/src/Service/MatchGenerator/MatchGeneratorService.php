<?php
namespace App\Service\MatchGenerator;
use App\Service\Interfaces\MatchGeneratorServiceInterface;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;


class MatchGenerator implements MatchGeneratorServiceInterface
{
    private DatabaseServiceInterface $databaseService;

    public function __construct(DatabaseServiceInterface $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function generateMatchScore(array $team1, array $team2, array $winnerTeams, array &$wins, int $maxWins): string
    {
        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];
        $score = '';
        if (in_array($team1['shortName'], $winnerTeams) && $wins[$team1['shortName']] < $maxWins) {
            $score = $possibleScores[mt_rand(0, 1)];
            $wins[$team1['shortName']]++;
        } elseif (in_array($team2['shortName'], $winnerTeams) && $wins[$team2['shortName']] < $maxWins) {
            $score = $possibleScores[mt_rand(2, 3)];
            $wins[$team2['shortName']]++;
        } else {
            $score = $possibleScores[mt_rand(0, 3)];
            if (in_array($score, ['2:0', '2:1'])) {
                $wins[$team1['shortName']]++;
            } else {
                $wins[$team2['shortName']]++;
            }
        }
        return $score;
    }
}
