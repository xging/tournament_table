<?php

namespace App\Repository\PlayoffResults;

use App\Entity\PlayoffResults;
use App\Repository\PlayoffResults\PlayoffResultsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayoffResults>
 */
class PlayoffResultsRepository extends ServiceEntityRepository implements PlayoffResultsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayoffResults::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

  /**
     * @param array $criteria
     * @return PlayoffResults[]
     */
    public function findBy(array $criteria, $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        return parent::findBy($criteria);
    }
}
