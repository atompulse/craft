<?php

namespace Craft\Services\DataSource;

use Craft\Services\DataSource\Messages\DataSourceRequestInterface;
use Craft\Services\DataSource\Messages\DataSourceResponseInterface;

/**
 * Interface DataSourceServiceInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface DataSourceServiceInterface
{
    /**
     * @param DataSourceRequestInterface $request
     * @return DataSourceResponseInterface
     */
    public function execute(DataSourceRequestInterface $request): DataSourceResponseInterface;
}
