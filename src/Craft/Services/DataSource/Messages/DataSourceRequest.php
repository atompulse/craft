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
        $this->defineProperty('page', ['number']);
        $this->defineProperty('pageSize', ['number']);
        $this->defineProperty('sorters', ['array', 'null']);
        $this->defineProperty('filters', ['array', 'null']);

        if ($data !== null) {
            $this->fromArray($data, true, false);
        }
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getSorters(): array
    {
        return $this->sorters;
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

}
