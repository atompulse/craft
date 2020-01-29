<?php

namespace Craft\Messaging;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Validation\DataValidatorInterface;

/**
 * Interface RequestInterface
 * @package Craft\Messaging
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface RequestInterface extends DataContainerInterface, DataValidatorInterface
{

}
