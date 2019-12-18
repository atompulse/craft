<?php

namespace Craft\Messaging;

use Atompulse\Component\Domain\Data\DataContainerInterface;
use Craft\Messaging\Service\ServiceError;

/**
 * Interface ResponseInterface
 * @package Craft\Messaging
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface ResponseInterface extends DataContainerInterface
{
    public function getStatus(): string;

    public function getErrors(): ?array;

    public function addError(ServiceError $error);
}
