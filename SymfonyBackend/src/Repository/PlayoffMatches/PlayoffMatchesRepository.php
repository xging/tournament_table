<?php

namespace App\Repository\PlayoffMatches;

use App\Entity\PlayoffMatches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayoffMatches>
 */
class PlayoffMatchesRepository extends ServiceEntityRepository implements PlayoffMatchesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayoffMatches::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

     /**
     * @param array $criteria
     * @return PlayoffMatches[]
     */
    public function findBy(array $criteria, $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        return parent::findBy($criteria);
    }
}
