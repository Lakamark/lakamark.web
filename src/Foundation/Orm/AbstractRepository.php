<?php

namespace App\Foundation\Orm;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Create interactable query and get data on the first entity hydration.
     *
     * @param string|null $indexBy the index for the FROM index
     */
    public function createIterableQuery(string $alias, $indexBy = null): IterableQueryBuilder
    {
        /** @var IterableQueryBuilder<E> $queryBuilder */
        $queryBuilder = new IterableQueryBuilder($this->getEntityManager());

        return $queryBuilder->select($alias)->from($this->getEntityName(), $alias, $indexBy);
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function hydrateRelation(array $items, string $propertyName): array
    {
        if (!isset($items[0])) {
            return $items;
        }

        $getter = 'get'.ucfirst($propertyName);
        $setter = 'set'.ucfirst($propertyName);
        $ids = array_map(fn ($item) => $item->$getter()->getId(), $items);
        /** @var class-string $entityClass */
        $entityClass = $items[0]::class;
        $reflection = new \ReflectionClass($entityClass);
        $relationType = $reflection->getProperty($propertyName)->getType();
        if (!$relationType instanceof \ReflectionNamedType) {
            throw new \Exception(sprintf('Impossible to hydrate the property type %s of %s', $propertyName, $entityClass));
        }
        /** @var class-string $relationClass */
        $relationClass = $relationType->getName();
        if (!$relationClass || !str_contains($relationClass, 'Entity')) {
            throw new \Exception(sprintf('Impossible to hydrate the relationship %s, property %s (%s) is not entity', $entityClass, $propertyName, $relationClass));
        }

        // Fin the relationship objects
        /** @var object[] $relationItems */
        $relationItems = $this->getEntityManager()->getRepository($relationClass)->findBy(['id' => $ids]);
        $relationItemsById = collect($relationItems)->keyBy(fn (object $item) => method_exists($item, 'getId') ?
            $item->getId() : -1)->toArray();

        // Stack the relationship
        foreach ($items as $item) {
            $item->$setter($relationItemsById[$item->$getter()->getId()] ?? null);
        }

        return $items;
    }

    private function findByCaseInsensitiveQuery(array $conditions): Query
    {
        $conditionString = [];
        $parameters = [];
        foreach ($conditions as $k => $v) {
            $conditionString[] = "LOWER(o.$k) = :$k";
            $parameters[] = new Query\Parameter($k, strtolower((string) $v));
        }

        return $this->createQueryBuilder('o')
            ->where(join(' AND ', $conditionString))
            ->setParameters(new ArrayCollection($parameters))
            ->getQuery();
    }
}
