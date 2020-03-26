<?php

namespace Craft\Tests\DataContainer;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class SimpleTypesContainerMandatoryStructure
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property int number
 * @property string text
 * @property bool flag
 */
class SimpleTypesContainer implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('number', ['int']);
        $this->defineProperty('text', ['string']);
        $this->defineProperty('flag', ['bool']);

        if ($data) {
            $this->fromArray($data, true, false);
        }
    }
}
