<?php

namespace Craft\Services\DataSource\Messages;

use Craft\Messaging\Service\ServiceRequest;
use Craft\Services\DataSource\Data\FilterValue;
use Craft\Services\DataSource\Data\SorterValue;

/**
 * Class DataSourceRequest
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property number page
 * @property number pageSize
 * @property array sorters
 * @property array filters
 */
class DataSourceRequest extends ServiceRequest implements DataSourceRequestInterface
{

    public function __construct(array $data = null)
    {
        $this->defineProperty('page', ['number'], 1);
        $this->defineProperty('pageSize', ['number'], 10);
        $this->defineProperty('sorters', ['array', 'null']);
        $this->defineProperty('filters', ['array', 'null']);

        // preset defaults
        $this->addPropertyValue('sorters', []);
        $this->addPropertyValue('filters', []);

        if ($data !== null) {
            $this->fromArray($data, true, false);
        }
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
    public function getPageSize(): int
    {
        return $this->getPropertyValue('pageSize');
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addFilter(string $key, $value): void
    {
        $this->addPropertyValue('filters', new FilterValue($key, $value));
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters = null): void
    {
        if ($filters && count($filters)) {
            foreach ($filters as $filterKey => $filterValue) {
                $this->addFilter($filterKey, $filterValue);
            }

        }
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getPropertyValue('filters') ?? [];
    }

    /**
     * @param string $key
     * @param string $direction
     * @throws \Exception
     */
    public function addSorter(string $key, string $direction = self::SORT_ASC): void
    {
        if ($direction === self::SORT_ASC || $direction === self::SORT_DESC) {
            $this->addPropertyValue('sorters', new SorterValue($key, $direction));
        }

        throw new \Exception("Unrecognized sorter direction [$direction]");
    }

    /**
     * @param array $sorters
     */
    public function setSorters(array $sorters = null): void
    {
        if ($sorters && count($sorters)) {
            foreach ($sorters as $key => $direction) {
                $this->addSorter($key, $direction);
            }
        }
    }

    /**
     * @return array
     */
    public function getSorters(): array
    {
        return $this->getPropertyValue('sorters') ?? [];
    }

}
