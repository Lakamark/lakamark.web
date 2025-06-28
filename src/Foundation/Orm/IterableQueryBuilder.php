<?php

namespace App\Foundation\Orm;

use Doctrine\ORM\QueryBuilder;

class IterableQueryBuilder extends QueryBuilder implements \IteratorAggregate, \ArrayAccess
{
    private bool $firstResultFetched = false;

    private ?object $firstResult = null;

    private ?array $results = null;

    public function getFirstResultOnly(): ?object
    {
        if (false === $this->firstResultFetched) {
            $this->firstResultFetched = true;
            $this->firstResult = $this->getQuery()->setMaxResults(1)->getOneOrNullResult();
        }

        return $this->firstResult;
    }

    public function getResults(): array
    {
        if (null === $this->results) {
            $this->results = $this->getQuery()->getResult();
        }

        return $this->results;
    }

    public function getIterator(): \Traversable
    {
        if (null === $this->results) {
            $this->results = $this->getQuery()->getResult();
        }

        return new \ArrayIterator($this->results);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->getResults());
    }

    public function offsetGet(mixed $offset): array|object
    {
        return $this->getResults()[$offset];
    }

    /**
     * @param string       $offset
     * @param object|array $value
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->getResults()[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->getResults()[$offset]);
    }
}
