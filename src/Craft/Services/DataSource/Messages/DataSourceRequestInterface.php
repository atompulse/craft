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

    public function getPage(): int;

    public function getPageSize(): int;

    public function getSorters(): array;

    public function getFilters(): array;

    public function addFilter(string $field, $value): void;

    public function addSorter(string $field, string $direction = self::SORT_ASC): void;
}
