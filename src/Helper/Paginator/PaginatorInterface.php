<?php

namespace App\Helper\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface to link between the knp_paginator bundle and your application.
 */
interface PaginatorInterface
{
    /**
     * Allow searching with filter.
     */
    public function allowShort(string ...$fields): self;

    public function paginate(Query $query): PaginationInterface;
}
