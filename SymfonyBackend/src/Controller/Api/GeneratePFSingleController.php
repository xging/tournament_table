<?php

namespace App\Controller\Api;

use App\Service\Database\DatabaseServiceT;
use App\Service\Playoff\Bronze\BronzeMedalMatchService;
use App\Service\Playoff\Grandfinal\GrandFinalMatchService;
use App\Service\Playoff\SingleMatchService;
use App\Service\Playoff\Semifinal\SemiFinalMatchService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

// Пример класса для управления данными
class PlayoffDataManager {
    private $databaseService;
    private $em;
    private $logger;
    public function __construct(DatabaseServiceT $databaseService, EntityManagerInterface $em, LoggerInterface $logger) {
        $this->databaseService = $databaseService;
        $this->em = $em;
        $this->logger = $logger;
    }
    
    public function clearData(): void {
        if ($this->getCountOfRows() >= 8) {
            $this->updateDWPickedFlag(false);
            $this->databaseService->setIndexId(0, 'Quarterfinal');
            $tables = [
                'playoff_matches',
                'quarterfinal_winners',
                'semifinal_winners',
                'semifinal_loosers',
                'playoff_results'
            ];

            $this->em->getConnection()->beginTransaction();
            try {
                foreach ($tables as $table) {
                    $this->databaseService->clearTable($table);
                }
                $this->em->getConnection()->commit();
            } catch (\Exception $e) {
                $this->logger->error('Error occurred: ' . $e->getMessage(), ['exception' => $e]);
                $this->em->getConnection()->rollBack();
                throw $e;
            }
            
        }
       
    }

    public function updateDWPickedFlag(bool $newValue): void {
        $query = $this->em->createQuery(
            'UPDATE App\Entity\DivisionWinners d
            SET d.picked_flag = :newValue'
        )->setParameter('newValue', $newValue);

        $query->execute();
    }
    public function getCountOfRows(): int {
        $dql = 'SELECT COUNT(e.id) FROM App\Entity\PlayoffMatches e';
        $query = $this->em->createQuery($dql);
        return (int) $query->getSingleScalarResult();
    }
}

// Обновление контроллера
class GeneratePFSingleController extends AbstractController {
    private $singleMatchService;
    private $semiFinalMatchService;
    private $bronzeMedalMatchService;
    private $grandFinalMatchService;
    private $playoffDataManager;
    private $logger;
    private $databaseService;

    public function __construct(
        SingleMatchService $singleMatchService,
        SemiFinalMatchService $semiFinalMatchService,
        BronzeMedalMatchService $bronzeMedalMatchService,
        GrandFinalMatchService $grandFinalMatchService,
        PlayoffDataManager $playoffDataManager,
        LoggerInterface $logger,
        DatabaseServiceT $databaseService
    ) {
        $this->singleMatchService = $singleMatchService;
        $this->semiFinalMatchService = $semiFinalMatchService;
        $this->bronzeMedalMatchService = $bronzeMedalMatchService;
        $this->grandFinalMatchService = $grandFinalMatchService;
        $this->playoffDataManager = $playoffDataManager;
        $this->logger = $logger;
        $this->databaseService = $databaseService;
    }

    #[Route('/api/generate-playoff-single-data', name: 'generate-playoff-single-data')]
    public function addData(): Response {
        try {
            $this->playoffDataManager->clearData();
            $matchesCount = $this->playoffDataManager->getCountOfRows();
            $singleMatchService = $this->singleMatchService->createMatches('Quarterfinal', 'Semifinal', 'BronzeMedal', 'Grandfinal', $matchesCount);

            if ($matchesCount < 4) {
                $result = ['Quarterfinal' => $singleMatchService['QuarterFinal']];
            } elseif ($matchesCount >= 4 && $matchesCount < 6) {
                $this->databaseService->setIndexId(0, 'Quarterfinal');   
                $result = ['Semifinal' => $singleMatchService['SemiFinal']];
            } elseif ($matchesCount >= 6 && $matchesCount < 7) {
                $result = ['BronzeMedal' => $singleMatchService['BronzeMedal']];
            } elseif ($matchesCount >= 7) {
                $result = ['Grandfinal' => $singleMatchService['Grandfinal']];
            } else {
                $result = [];
            }

            return new JsonResponse($result);

        } catch (\Exception $e) {
            $this->logger->error('Error occurred: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
