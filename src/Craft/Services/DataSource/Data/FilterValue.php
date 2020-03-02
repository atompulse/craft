<?php

namespace Craft\Services\DataSource\Data;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class FilterValue
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string key
 * @property string value
 */
class FilterValue implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('key', ['string']);
        $this->defineProperty('value');

        if (!is_null($data)) {
            $this->fromArray($data, true, false);
        }
    }
}
