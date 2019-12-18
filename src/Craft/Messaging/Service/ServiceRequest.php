<?php

namespace Craft\Messaging\Service;

use Atompulse\Component\Domain\Data\DataContainer;
use Craft\Http\Controller\ActionArgumentRequestInterface;
use Craft\Messaging\RequestInterface;

/**
 * Class ServiceRequest
 * @package Craft\Messaging\Service
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ServiceRequest implements RequestInterface, ActionArgumentRequestInterface
{
    use DataContainer;
}
