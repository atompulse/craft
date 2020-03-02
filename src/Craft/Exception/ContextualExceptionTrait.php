<?php

namespace Craft\Exception;

/**
 * Trait ContextualExceptionTrait
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
trait ContextualExceptionTrait
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * Get the list of ContextualErrorInterface errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add a ContextualErrorInterface error
     *
     * @param ContextualErrorInterface $error
     * @return $this
     */
    public function addError(ContextualErrorInterface $error)
    {
        $this->errors[] = $error;

        return $this;
    }
}
