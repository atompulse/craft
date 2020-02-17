<?php

namespace Craft\Services\DataSource\Messages;

use Craft\Messaging\RequestInterface;

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
    const SORT_ASC = 'asc';
    const SORT_DESC = 'asc';

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @param string $field
     * @param string $direction
     */
    public function addSorter(string $field, string $direction = self::SORT_ASC): void;

    /**
     * @param array $sorters
     */
    public function setSorters(array $sorters): void;

    /**
     * @return array
     */
    public function getSorters(): array;

    /**
     * @param string $field
     * @param $value
     */
    public function addFilter(string $field, $value): void;

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void;

    /**
     * @return array
     */
    public function getFilters(): array;


}
