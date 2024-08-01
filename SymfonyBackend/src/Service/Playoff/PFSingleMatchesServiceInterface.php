<?PHP
namespace App\Service\Playoff;

interface PFSingleMatchesServiceInterface
{
    public function createMatches(string $stage, string $stage_2, string $stage_3, string $stage_4, int $matchesCount): array;
}