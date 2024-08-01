<?PHP
namespace App\Controller\Api;

use App\Service\Playoff\Results\BracketResultsDataService;
use App\Service\Playoff\Bronze\BronzeMedalDataService;
use App\Service\Playoff\Grandfinal\GrandFinalDataService;
use App\Service\Playoff\Quarterfinal\QuarterfinalDataService;
use App\Service\Playoff\Semifinal\SemiFinalDataService;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetPlayoffDataController extends AbstractController
{
    private $quarterfinalDataService;
    private $semiFinalDataService;
    private $bronzeMedalDataService;
    private $grandFinalDataService;
    private $bracketResultsDataService;

    public function __construct(QuarterfinalDataService $quarterfinalDataService, SemiFinalDataService $semiFinalDataService, BronzeMedalDataService $bronzeMedalDataService, GrandFinalDataService $grandFinalDataService, BracketResultsDataService $bracketResultsDataService)
    {
        $this->quarterfinalDataService = $quarterfinalDataService;
        $this->semiFinalDataService = $semiFinalDataService;
        $this->bronzeMedalDataService = $bronzeMedalDataService;
        $this->grandFinalDataService = $grandFinalDataService;
        $this->bracketResultsDataService = $bracketResultsDataService;
    }
 
    #[Route('/api/get-playoff-data', name: 'get-playoff-data', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $quarterMatchResult = $this->quarterfinalDataService->getMatchData('Quarterfinal');
        $semiFinalMatchResult = $this->semiFinalDataService->getMatchData('Semifinal');
        $bronzeMedalMatchResult = $this->bronzeMedalDataService->getMatchData('BronzeMedal');
        $grandFinalMatchResult = $this->grandFinalDataService->getMatchData('Grandfinal');
        $bracketResults = $this->bracketResultsDataService->getMatchResults();

        return new JsonResponse([
            'Quarterfinal' => $quarterMatchResult['Quarterfinal'],
            'Semifinal' => $semiFinalMatchResult['Semifinal'],
            'BronzeMedal' => $bronzeMedalMatchResult['BronzeMedal'],
            'Grandfinal' => $grandFinalMatchResult['Grandfinal'],
            'Results' => $bracketResults['Results']            
            ]);

    }
}