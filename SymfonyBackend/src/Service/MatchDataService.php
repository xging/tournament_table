<?php
namespace App\Service;
use App\Repository\Divisions\DivisionsRepositoryInterface;
use App\Repository\Teams\TeamsRepositoryInterface;
use App\Repository\Matches\MatchesRepositoryInterface;
use App\Repository\DivisionWinners\DivisionWinnersRepositoryInterface;
use App\Entity\Divisions;
use App\Entity\Teams;
use App\Entity\Matches;
use App\Entity\DivisionWinners;
use App\Service\Interfaces\MatchServiceInterface;

class MatchDataService implements MatchServiceInterface
{
    private DivisionsRepositoryInterface $divisionsRepository;
    private TeamsRepositoryInterface $teamsRepository;
    private MatchesRepositoryInterface $matchesRepository;
    private DivisionWinnersRepositoryInterface $divisionWinnersRepository;

    public function __construct(
        DivisionsRepositoryInterface $divisionsRepository,
        TeamsRepositoryInterface $teamsRepository,
        MatchesRepositoryInterface $matchesRepository,
        DivisionWinnersRepositoryInterface $divisionWinnersRepository
    ) {
        $this->divisionsRepository = $divisionsRepository;
        $this->teamsRepository = $teamsRepository;
        $this->matchesRepository = $matchesRepository;
        $this->divisionWinnersRepository = $divisionWinnersRepository;
    }

    /**
     *
     * @return array
     */
    public function getMatchData(): array
    {
        $divisions = $this->divisionsRepository->findAll();
        $data = [];

        foreach ($divisions as $division) {
            $teams = $this->teamsRepository->findBy(['division' => $division]);
            $teamsArray = [];
            $matchesArray = [];
            $divisionWinners = $this->getDivisionWinners($division);

            // Collect team names
            foreach ($teams as $team) {
                $teamsArray[] = $team->getName();
                $matchesArray[] = [$team->getName() => $this->getTeamMatches($team)];
            }

            $data[] = [
                'name' => $division->getName(),
                'teams' => $teamsArray,
                'matches' => $matchesArray,
                'winners' => $divisionWinners
            ];
        }

        return ['divisions' => $data];
    }

    /**
     *
     * @param Divisions $division
     * @return array
     */
    private function getDivisionWinners(Divisions $division): array
    {
        $winners = $this->divisionWinnersRepository->findBy(['division_id' => $division]);
        return array_map(fn(DivisionWinners $winner) => $winner->getTeamName(), $winners);
    }

    /**
     *
     * @param Teams $team
     * @return array
     */
    private function getTeamMatches(Teams $team): array
    {
        $matches = $this->matchesRepository->findBy(['team_1_id' => $team->getId()]);
        $matchData = [];

        foreach ($matches as $match) {
            $opponent = $match->getTeam2Id();
            $opponentTeam = $this->teamsRepository->find($opponent);
            if ($opponentTeam) {
                $opponentName = $opponentTeam->getName();
                $matchData[$opponentName] = $match->getResult();
            } else {
                $matchData['Unknown Opponent'] = $match->getResult();
            }
        }

        return $matchData;
    }
}
