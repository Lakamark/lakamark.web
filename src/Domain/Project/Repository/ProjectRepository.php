<?php

namespace App\Domain\Project\Repository;

use App\Domain\Project\Entity\Project;
use App\Foundation\Orm\AbstractRepository;
use App\Foundation\Orm\IterableQueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Project>
 */
class ProjectRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function queryAll(): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.isOnline = true')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
        ;
    }

    public function findRecentProjects(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('p')
            ->select('p')
            ->where('p.isOnline = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
