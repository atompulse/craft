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
class ArrayItemContainerNotNullableWithoutSetter implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('items', ['array']);

        if ($data) {
            $this->fromArray($data, true, false);
        }
    }
}
