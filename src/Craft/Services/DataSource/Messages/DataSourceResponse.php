<?php

namespace Craft\Services\DataSource\Messages;

use Craft\Data\Container\DataContainerTrait;
use Craft\Messaging\Service\ServiceResponse;

/**
 * Class DataSourceResponse
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property array data
 * @property number page
 * @property number pages
 * @property number pageSize
 * @property array sorters
 * @property array filters
 *
 */
class DataSourceResponse extends ServiceResponse implements DataSourceResponseInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        parent::__construct();

        $this->defineProperty('data', ['array']);
        $this->defineProperty('page', ['number']);
        $this->defineProperty('pages', ['number']);
        $this->defineProperty('pageSize', ['number']);
        $this->defineProperty('sorters', ['array', 'null']);
        $this->defineProperty('filters', ['array', 'null']);

        if ($data !== null) {
            $this->fromArray($data, true, false);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->getPropertyValue('data');
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->getPropertyValue('page');
    }

    /**
     * @return int
     */
    public function getPages(): int
    {
        return $this->getPropertyValue('pages');
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->getPropertyValue('pageSize');
    }

    /**
     * @return array
     */
    public function getSorters(): array
    {
        return $this->getPropertyValue('sorters');
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getPropertyValue('filters');
    }
}
