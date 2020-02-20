<?php

namespace Craft\Messaging;

use Craft\Data\Container\DataContainerInterface;
use Craft\Exception\ContextualErrorInterface;


/**
 * Interface ResponseInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ResponseInterface extends DataContainerInterface
{
    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return array|null
     */
    public function getErrors(): ?array;

    /**
     * @param ContextualErrorInterface $error
     */
    public function addError(ContextualErrorInterface $error): void;
}
