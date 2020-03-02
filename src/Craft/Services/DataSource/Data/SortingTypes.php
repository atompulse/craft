<?php

namespace Craft\Services\DataSource\Data;

use ReflectionClass;

/**
 * Class SortingTypes
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class SortingTypes
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    /**
     * Return the list of types accepted
     * @return array
     */
    public static function get(): array
    {
        return (new ReflectionClass(__CLASS__))->getConstants();
    }
}
