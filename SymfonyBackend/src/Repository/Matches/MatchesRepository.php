<?php

namespace App\Repository\Matches;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Matches>
 */
class MatchesRepository extends ServiceEntityRepository implements MatchesRepositoryInterface
{
    private LoggerInterface $logger;
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Matches::class);
        $this->logger = $logger;
    }

    /**
     * @param array $criteria
     * @return Matches[]
     */
    public function findBy(array $criteria, $orderBy = null, int|null $limit = null, int|null $offset = null): array
    {
        return parent::findBy($criteria);
    }

    public function matchExists(int $team1Id, int $team2Id): bool
    {
        try {
            $qb = $this->createQueryBuilder('m')
                ->where('(m.team_1_id = :team1Id AND m.team_2_id = :team2Id)')
                ->orWhere('(m.team_1_id = :team2Id AND m.team_2_id = :team1Id)')
                ->setParameter('team1Id', $team1Id)
                ->setParameter('team2Id', $team2Id)
                ->select('COUNT(m.id) AS count');

            $count = $qb->getQuery()->getSingleScalarResult();

            return $count > 0;
        } catch (\Exception $e) {
            $this->logger->error('XX Error in matchExists method: ' . $e->getMessage());
            return false;
        }
    }
}
