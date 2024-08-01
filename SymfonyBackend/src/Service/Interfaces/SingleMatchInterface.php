<?PHP
namespace App\Service\Interfaces;

interface SingleMatchInterface
{
    public function createMatches(array $teams, string $divisionName, int $divisionId): array;
}