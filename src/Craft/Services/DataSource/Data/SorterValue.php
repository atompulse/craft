<?php

namespace Craft\Services\DataSource\Data;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;
use LogicException;


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

    public function __construct(array $data = null)
    {
        $this->defineProperty('key', ['string']);
        $this->defineProperty('value', ['string']);

        if (!is_null($data)) {
            $this->fromArray($data, true, false);
        }
    }

    /**
     * @param $value
     */
    public function setValue($value): void
    {
        if (in_array($value, SortingTypes::get())) {
            $this->addPropertyValue('value', $value);
        } else {
            throw new LogicException("Unrecognized SortingType [$value]");
        }

    }
}
