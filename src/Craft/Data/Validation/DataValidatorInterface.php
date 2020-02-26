<?php

namespace Craft\Data\Validation;

/**
 * Interface DataValidatorInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface DataValidatorInterface
{
    /**
     * MUST return a list of Symfony Validation Constraints
     * @return array
     */
    public function getValidatorConstraints(): array;
}
