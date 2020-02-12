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
    public function getData(): array;

    public function getPage(): int;

    public function getPages(): int;

    public function getPageSize(): int;

    public function getSorters(): array;

    public function getFilters(): array;
}
