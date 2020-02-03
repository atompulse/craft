<?php

namespace Craft\Messaging\Service;

use Craft\Data\Container\DataContainerTrait;
use Craft\Data\Validation\DataValidatorTrait;
use Craft\Http\Controller\ActionArgumentRequestInterface;

/**
 * Class ServiceRequest
 * @package Craft\Messaging\Service
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ServiceRequest implements ActionArgumentRequestInterface
{
    use DataContainerTrait;
    use DataValidatorTrait;
}
