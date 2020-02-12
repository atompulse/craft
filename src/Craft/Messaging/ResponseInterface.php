<?php

namespace Craft\Messaging;

use Craft\Data\Container\DataContainerInterface;
use Craft\Messaging\Service\ServiceError;

/**
 * Interface ResponseInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ResponseInterface extends DataContainerInterface
{
    public function getStatus(): string;

    public function getErrors(): ?array;

    public function addError(ServiceError $error);
}
