<?PHP
namespace App\Service\Interfaces;

interface DivisionServiceInterface
{
    public function createDivisions(): array;
    public function getDivisions(): array;
}