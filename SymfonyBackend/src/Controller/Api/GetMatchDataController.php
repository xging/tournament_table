<?PHP
namespace App\Controller\Api;

use App\Service\MatchDataService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetMatchDataController extends AbstractController
{
    private $matchDataService;

    public function __construct(MatchDataService $matchDataService)
    {
        $this->matchDataService = $matchDataService;
    }

    #[Route('/api/get-match-data', name: 'get-match-data', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $data = $this->matchDataService->getMatchData();
        return new JsonResponse($data);
    }
}