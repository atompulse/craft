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
     * @return array
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[] = $error->normalizeData();
        }

        return $errors;
    }

    /**
     * @param ContextualErrorInterface $error
     * @return $this
     */
    public function addError(ContextualErrorInterface $error)
    {
        $this->errors[] = $error;

        return $this;
    }
}