<?php

namespace Craft\Services\DataSource\Messages;

use Craft\Messaging\RequestInterface;
use Craft\Services\DataSource\Data\FilterValue;
use Craft\Services\DataSource\Data\SorterValue;

/**
 * Interface DataSourceRequestInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property number page
 * @property number pageSize
 * @property array sorters
 * @property array filters
 *
 */
interface DataSourceRequestInterface extends RequestInterface
{
    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @param SorterValue $sorter
     */
    public function addSorter(SorterValue $sorter): void;

    /**
     * @param array $sorters
     */
    public function setSorters(array $sorters): void;

    /**
     * @return array
     */
    public function getSorters(): array;

    /**
     * @param FilterValue $filter
     */
    public function addFilter(FilterValue $filter): void;

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void;

    /**
     * @return array
     */
    public function getFilters(): array;


}
