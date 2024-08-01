<?PHP
namespace App\Service;

use App\Service\Database\DatabaseServiceInterface;
use App\Service\FileLoader\FileLoaderServiceInterface;
use App\Entity\Divisions;
use App\Entity\Teams;
use App\Repository\Teams\TeamsRepository;
use App\Service\Interfaces\TeamServiceInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class TeamService implements TeamServiceInterface
{
    private DatabaseServiceInterface $databaseService;
    private FileLoaderServiceInterface $fileLoader;
    private TeamsRepository $teamsRepository;
    private LoggerInterface $logger;
    public function __construct(DatabaseServiceInterface $databaseService, FileLoaderServiceInterface $fileLoader, TeamsRepository $teamsRepository, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->fileLoader = $fileLoader;
        $this->teamsRepository = $teamsRepository;
        $this->logger = $logger;
    }

    /**
     *
     * @param object $divisionA
     * @param object $divisionB
     * @return array
     * @throws RuntimeException
     */
    public function loadAndAssignTeams(object $divisionA, object $divisionB): array
    {
        $existingTeams = $this->checkTeamsTable($divisionA, $divisionB);

        if (empty($existingTeams)) {
            $teams = $this->fileLoader->loadTeams();

            if (!is_array($teams)) {
                throw new RuntimeException('Failed to load teams from file.');
            }

            shuffle($teams); 
            $selectedTeams = array_slice($teams, 0, 16);

            if (count($selectedTeams) < 16) {
                throw new RuntimeException('Not enough teams to select.');
            }

            $teamsA = array_slice($selectedTeams, 0, 8);
            $teamsB = array_slice($selectedTeams, 8, 8);

            return $this->assignTeamsToDivisions($teamsA, $teamsB, $divisionA, $divisionB);
        } else {
            return $existingTeams;
        }
    }

    /**
     *
     * @return array
     * @throws RuntimeException
     */
    private function checkTeamsTable(object $divisionA, object $divisionB): array
{
    try {
        $teamsList = $this->teamsRepository->findAll();
    } catch (\Exception $e) {
        $this->logger->error('Failed to fetch teams from repository', ['exception' => $e]);
        throw new RuntimeException('Failed to fetch teams from repository.');
    }

    if (empty($teamsList)) {
        $this->logger->info('Teams checked in table and it is empty');
        return [];
    }

    $allTeams = [
        'Division A' => [
            'divisionId' => $divisionA,
            'teams' => []
        ],
        'Division B' => [
            'divisionId' => $divisionB,
            'teams' => []
        ]
    ];

    foreach ($teamsList as $team) {

        if ($team->getDivision()->getDivisionId() == $divisionA->getDivisionId()) {
            $allTeams['Division A']['teams'][] = ['shortName' => $team->getShortName(), 'fullName' => $team->getName()];
            $this->logger->info('Division A here');

        } elseif ($team->getDivision()->getDivisionId() == $divisionB->getDivisionId()) {
            $allTeams['Division B']['teams'][] = ['shortName' => $team->getShortName(), 'fullName' => $team->getName()];
            $this->logger->info('Teams checked in table', $allTeams);
            $this->logger->info('Division B here');

        }
    }

    $this->logger->info('Teams checked in table3', $allTeams);

    return $allTeams;
}

    private function assignTeamsToDivisions(array $teamsA, array $teamsB, object $divisionA, object $divisionB): array
    {
        $allTeams = [
            'Division A' => [
                'divisionId' => $divisionA,
                'teams' => []
            ],
            'Division B' => [
                'divisionId' => $divisionB,
                'teams' => []
            ]
        ];

        foreach ($teamsA as $i => $team) {
            $index = $i+1;
            $this->databaseService->addTeam($team['fullName'], $divisionA, $team['shortName'], false, $index, 0);
            $allTeams['Division A']['teams'][] = $team;
        }
        foreach ($teamsB as $i => $team) {
            $index = $i+9;
            $this->databaseService->addTeam($team['fullName'], $divisionB, $team['shortName'], false, $index, 0);
            $allTeams['Division B']['teams'][] = $team;
        }

        return $allTeams;
    }
}
