<?php

namespace Craft\Services\DataSource\Data;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class SorterValue
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string key
 * @property string value
 */
class SorterValue implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(string $key, string $value)
    {
        $this->defineProperty('key', ['string']);
        $this->defineProperty('value', ['string']);

        $this->addPropertyValue('key', $key);
        $this->addPropertyValue('value', $value);
    }
}
