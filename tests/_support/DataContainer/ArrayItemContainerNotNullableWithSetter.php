<?php

namespace Craft\Tests\DataContainer;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class ArrayItemContainerNotNullable
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property array items
 */
class ArrayItemContainerNotNullableWithSetter implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('items', ['array']);

        if ($data) {
            $this->fromArray($data, true, false);
        }
    }

    public function setItems(array $items = null)
    {
        if ($items) {
            foreach ($items as $item) {
                $this->addItem($item);
            }
        }
    }

    public function addItem($item)
    {
        $this->addPropertyValue('items', $item);
    }

}
