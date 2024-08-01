<?php

namespace App\Repository\DivisionWinners;

use App\Entity\DivisionWinners;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DivisionWinners>
 */
class DivisionWinnersRepository extends ServiceEntityRepository implements DivisionWinnersRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DivisionWinners::class);
    }

   /**
     * @param array $criteria
     * @return DivisionWinners[]
     */
    public function findBy(array $criteria, $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        return parent::findBy($criteria);
    }
}