<?php

namespace Craft\Messaging\Service;

use Craft\Data\Container\DataContainerTrait;
use Craft\Data\Validation\DataValidatorTrait;
use Craft\Messaging\RequestInterface;

/**
 * Class ServiceRequest
 * @package Craft\Messaging\Service
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ServiceRequest implements RequestInterface
{
    use DataContainerTrait;
    use DataValidatorTrait;
}
