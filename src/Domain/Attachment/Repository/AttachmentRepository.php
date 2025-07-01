<?php

namespace App\Domain\Attachment\Repository;

use App\Domain\Attachment\Entity\Attachment;
use App\Foundation\Orm\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Attachment>
 */
class AttachmentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findMonthsFromYear(): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('EXTRACT(MONTH FROM a.createdAt) as month, EXTRACT(YEAR FROM a.createdAt) as year, COUNT(a.id) as count')
            ->groupBy('month', 'year')
            ->orderBy('month', 'DESC')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getResult();

        return array_map(fn ($row) => [
            'path' => $row['year'].'/'.str_pad((string) $row['month'], 2, '0', STR_PAD_LEFT),
            'count' => $row['count'],
        ], $rows);
    }

    /**
     * @param string $path
     * @return array<Attachment>
     */
    public function findPath(string $path): array
    {
        $parts = explode('/', $path);
        $start = new \DateTimeImmutable("{$parts[0]}-{$parts[1]}-01");
        $end = $start->modify('+1 month -1 second');

        return $this->createQueryBuilder('a')
            ->where('a.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Attachment>
     */
    public function findLatestAttachment(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }
}
