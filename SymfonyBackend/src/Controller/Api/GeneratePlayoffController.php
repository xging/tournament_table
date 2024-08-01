<?PHP
namespace App\Controller\Api;
use App\Service\Database\DatabaseServiceT;
use App\Service\Playoff\Bronze\BronzeMedalMatchService;
use App\Service\Playoff\Grandfinal\GrandFinalMatchService;
use App\Service\Playoff\Quarterfinal\QuarterFinalMatchService;
use App\Service\Playoff\Semifinal\SemiFinalMatchService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratePlayoffController extends AbstractController
{
    private $quarterFinalMatchService;
    private $semiFinalMatchService;
    private $bronzeMedalMatchService;
    private $grandFinalMatchService;
    private $databaseService;
    public function __construct(QuarterFinalMatchService $quarterFinalMatchService, SemiFinalMatchService $semiFinalMatchService, BronzeMedalMatchService $bronzeMedalMatchService, GrandFinalMatchService $grandFinalMatchService, DatabaseServiceT $databaseService)
    {
        $this->quarterFinalMatchService = $quarterFinalMatchService;
        $this->semiFinalMatchService = $semiFinalMatchService;
        $this->databaseService = $databaseService;
        $this->bronzeMedalMatchService = $bronzeMedalMatchService;
        $this->grandFinalMatchService = $grandFinalMatchService;
    }

    #[Route('/api/generate-playoff-data', name: 'generate-playoff-data')]
    public function addData(): Response
    {
        try {
            $this->clearData();

            $quarterMatchResult = $this->quarterFinalMatchService->createMatches('QuarterFinal');
            $semiFinalMatchResult = $this->semiFinalMatchService->createMatches('SemiFinal');
            $bronzeMedalMatchResult = $this->bronzeMedalMatchService->createMatches('BronzeMedal');
            $grandFinalMatchResult = $this->grandFinalMatchService->createMatches('GrandFinal');

            return new JsonResponse([
            'Quarterfinal' => $quarterMatchResult['QuarterFinal'],
            'Semifinal' => $semiFinalMatchResult['SemiFinal'],
            'BronzeMedal' => $bronzeMedalMatchResult['BronzeMedal'],
            'Grandfinal' => $grandFinalMatchResult['Grandfinal']        
            ]);

        } catch (\Exception $e) {
            // $this->logger->error('Error occurred: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function clearData() {
        $tables = ['playoff_matches','quarterfinal_winners','semifinal_winners','semifinal_loosers','playoff_results'];
        foreach($tables as $table)
        {
            $this->databaseService->clearTable($table);
        }
    }
}