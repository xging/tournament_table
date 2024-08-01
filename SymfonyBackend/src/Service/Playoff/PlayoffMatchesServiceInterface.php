<?PHP
namespace App\Service\Playoff;

interface PlayoffMatchesServiceInterface
{
    public function createMatches(string $stage): array;
}