<?php

namespace Craft\Exception;

use Throwable;

/**
 * Interface ContextualExceptionInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ContextualExceptionInterface extends Throwable
{
    /**
     * Add a ContextualErrorInterface error
     * @param ContextualErrorInterface $error
     * @return mixed
     */
    public function addError(ContextualErrorInterface $error);

    /**
     * Get the list of ContextualErrorInterface errors
     * @return array
     */
    public function getErrors(): array;
}
