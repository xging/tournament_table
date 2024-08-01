<?PHP
namespace App\Controller\Api;

use App\Service\DivisionService;
use App\Service\TeamService;
use App\Service\MatchGenerator\SingleMatchService;
use App\Service\Database\DatabaseServiceT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateSingleMatchController extends AbstractController
{
    private $divisionService;
    private $teamService;
    private $singleMatchService;
    private $databaseService;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    public function __construct(DivisionService $divisionService, TeamService $teamService, SingleMatchService $singleMatchService, DatabaseServiceT $databaseService, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->divisionService = $divisionService;
        $this->teamService = $teamService;
        $this->singleMatchService = $singleMatchService;
        $this->databaseService = $databaseService;
        $this->em = $em;
        $this->logger = $logger;
    }

    #[Route('/api/create-single-match', name: 'create-single-match')]
    public function addData(): Response
    {
        try {

            $rowCount = $this->getCountOfRows();
            $this->logger->info("Row count before condition: $rowCount");

            if ($rowCount >= 112) {
                $this->clearData();
                // [$divisionA, $divisionB] = $this->divisionService->createDivisions();
                [$divisionA, $divisionB] = $this->divisionService->getDivisions();
            } else {
                [$divisionA, $divisionB] = $this->divisionService->getDivisions();
                $this->logger->info("Row count is less than 112: $rowCount");
            }

            $teams = $this->teamService->loadAndAssignTeams($divisionA, $divisionB);
      
            $matchBresult = $this->singleMatchService->createMatches($teams, 'Division B', $divisionB->id);
            $matchAresult = $this->singleMatchService->createMatches($teams, 'Division A', $divisionA->id);





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

    private function clearData()
    {
        $tables = $tables = ['teams', 'matches', 'division_winners', 'playoff_matches', 'quarterfinal_winners', 'semifinal_winners', 'semifinal_loosers', 'playoff_results'];
        $this->databaseService->setIndexId(0, 'Division A');

        foreach ($tables as $table) {
            $this->databaseService->clearTable($table);
        }
    }


    private function getCountOfRows(): int
    {
        try {
            $dql = 'SELECT COUNT(e.id) FROM App\Entity\Matches e';
            $query = $this->em->createQuery($dql);
            $count = (int) $query->getSingleScalarResult();
            $this->logger->info("Row count: $count");
            return $count;
        } catch (\Exception $e) {
            $this->logger->error('Error getting row count: ' . $e->getMessage());
            return 0;
        }
    }
}