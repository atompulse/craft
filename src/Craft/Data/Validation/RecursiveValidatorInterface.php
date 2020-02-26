<?php

namespace Craft\Data\Validation;

/**
 * Interface RecursiveValidatorInterface
 *
 * A symfony constraints validator capable of handling N-dimensional arrays of constraint validators
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface RecursiveValidatorInterface
{
    /**
     * Validate N-dimensional arrays of constraint validators
     * @param array $data
     * @param array $constraints
     * @param string|null $context
     * @return array An array indexed by keys of the $constraints keys, with values as array of ContextualError items
     */
    public function validate(array $data, array $constraints, string $context = null): array;
}
