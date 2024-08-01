<?PHP
namespace App\Service\Interfaces;

interface AllMatchesServiceInterface
{
    public function createMatches(array $teams, string $divisionName, int $divisionId): array;
}