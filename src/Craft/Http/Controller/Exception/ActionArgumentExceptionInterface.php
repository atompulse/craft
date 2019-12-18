<?php

namespace Craft\Http\Controller\Exception;

use Craft\Messaging\Service\ServiceError;

/**
 * Interface ActionArgumentExceptionInterface
 * @package Craft\Http\Controller\Exception
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface ActionArgumentExceptionInterface
{
    public function getArgumentsErrors(): array;
    public function addError(ServiceError $error);
}
