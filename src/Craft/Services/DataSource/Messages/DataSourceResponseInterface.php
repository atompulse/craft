<?php

namespace Craft\Services\DataSource\Messages;

use Craft\Messaging\ResponseInterface;

/**
 * Interface DataSourceResponseInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property array data
 * @property int page
 * @property int pages
 * @property int pageSize
 * @property array sorters
 * @property array filters
 *
 */
interface DataSourceResponseInterface extends ResponseInterface
{
    /**
     * @return array
     */
    public function getData(): array;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @return int
     */
    public function getPages(): int;

    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @return array
     */
    public function getSorters(): array;

    /**
     * @return array
     */
    public function getFilters(): array;
}
