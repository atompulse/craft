<?php

namespace Craft\Exception;

/**
 * Interface ContextualExceptionInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ContextualExceptionInterface extends \Throwable
{
    /**
     * @param ContextualErrorInterface $error
     * @return mixed
     */
    public function addError(ContextualErrorInterface $error);

    /**
     * @return array
     */
    public function getErrors(): array;
}
