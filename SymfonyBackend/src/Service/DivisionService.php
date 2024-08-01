<?php
namespace App\Service;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\Interfaces\DivisionServiceInterface;
use Psr\Log\LoggerInterface;

class DivisionService implements DivisionServiceInterface
{
    private $databaseService;
    private $logger;

    public function __construct(DatabaseServiceInterface $databaseService, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
    }

    public function createDivisions(): array
    {
        try {
        $divisionA = $this->databaseService->addDivision('Division A',1);
        $divisionB = $this->databaseService->addDivision('Division B',2);

        } catch (\Exception $e) {
            $this->logger->error('Error occurred: divisions:  ' . $e->getMessage());
        }

        // $divisionA = $this->databaseService->addDivision('Division A',1);
        // $divisionB = $this->databaseService->addDivision('Division B',2);
        
        // if ($divisionA === null || $divisionB === null) {
        //     throw new \Exception('Division creation failed.');
        // }

        return [$divisionA, $divisionB];
    }

    public function getDivisions(): array
    {
        try {
        $divisionA = $this->databaseService->getDivisions('Division A');
        $divisionB = $this->databaseService->getDivisions('Division B');

        } catch (\Exception $e) {
            $this->logger->error('Error occurred: divisions2:  ' . $e->getMessage());
        }

        return [$divisionA, $divisionB];
    }
}
