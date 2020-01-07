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
    /**
     * @return array
     */
    public function getArgumentsErrors(): array;

    /**
     * @param ServiceError $error
     * @return mixed
     */
    public function addError(ServiceError $error);
}
