<?PHP
namespace App\Controller\Api;

use App\Service\DivisionService;
use App\Service\TeamService;
use App\Service\MatchGenerator\AllMatchesService;
use App\Service\Database\DatabaseServiceT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateMatchController extends AbstractController
{
    private $divisionService;
    private $teamService;
    private $allMatchesService;
    private $databaseService;
    public function __construct(DivisionService $divisionService, TeamService $teamService, AllMatchesService $allMatchesService, DatabaseServiceT $databaseService)
    {
        $this->divisionService = $divisionService;
        $this->teamService = $teamService;
        $this->allMatchesService = $allMatchesService;
        $this->databaseService = $databaseService;
    }

    #[Route('/api/generate-match-data', name: 'generate-match-data')]
    public function addData(): Response
    {
        try {
            $this->clearData();

            // [$divisionA, $divisionB] = $this->divisionService->createDivisions();
            [$divisionA, $divisionB] = $this->divisionService->getDivisions();
            $teams = $this->teamService->loadAndAssignTeams($divisionA, $divisionB);

            $matchAresult = $this->allMatchesService->createMatches($teams, 'Division A', $divisionA->id);
            $matchBresult = $this->allMatchesService->createMatches($teams, 'Division B', $divisionB->id);

            return new JsonResponse([
                $divisionA->name => [
                    'matches' => $matchAresult['matches'],
                    'wins' => $matchAresult['wins'],
                    'topTeams' => $matchAresult['topTeams']
                ],
                $divisionB->name => [
                    'matches' => $matchBresult['matches'],
                    'wins' => $matchBresult['wins'],
                    'topTeams' => $matchBresult['topTeams']
                ]
            ]);

        } catch (\Exception $e) {
            // $this->logger->error('Error occurred: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function clearData() {
        $tables = ['teams','matches','division_winners','playoff_matches','quarterfinal_winners','semifinal_winners','semifinal_loosers','playoff_results'];
        foreach($tables as $table)
        {
            $this->databaseService->clearTable($table);
        }

        $this->databaseService->addIndexId(0,'Division B');
        $this->databaseService->addIndexId(0,'Division A');
        $this->databaseService->addIndexId(0,'Quarterfinal');
        $this->databaseService->addIndexId(0,'Semifinal');
        $this->databaseService->addIndexId(0,'BronzeMedal');
        $this->databaseService->addIndexId(0,'Grandfinal');
    }
}