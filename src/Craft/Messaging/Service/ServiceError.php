<?php

namespace Craft\Messaging\Service;

use Atompulse\Component\Domain\Data\DataContainer;
use Atompulse\Component\Domain\Data\DataContainerInterface;

/**
 * Class ServiceError
 * @package Craft\Messaging\Service
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string $message
 */
class ServiceError implements DataContainerInterface
{
    use DataContainer;

    public function __construct(string $message, string $context = '')
    {
        $this->defineProperty('message', ['string'], $message);
        $this->defineProperty('context', ['string', 'null'], $context);
    }
}
