<?php

namespace App\Domain\Blog\Repository;

use App\Domain\Blog\Entity\Category;
use App\Domain\Blog\Entity\Post;
use App\Foundation\Orm\AbstractRepository;
use App\Foundation\Orm\IterableQueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Post>
 */
class PostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findRecentPosts(int $limit): IterableQueryBuilder
    {
        return $this->createIterableQuery('p')
            ->select('p')
            ->where('p.isOnline = true')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);
    }

    public function queryAll(?Category $category = null): Query
    {
        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.isOnline = true')
            ->orderBy('p.createdAt', 'DESC');

        if ($category) {
            $query = $query->where('p.category = :category')
                ->setParameter('category', $category);
        }

        return $query->getQuery();
    }
}
