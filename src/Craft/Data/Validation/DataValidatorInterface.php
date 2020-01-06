<?php

namespace Craft\Data\Validation;

use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Interface DataValidatorInterface
 * @package Craft\Data\Validation
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface DataValidatorInterface
{
    /**
     * A list of
     * @return array
     */
    public function getValidatorConstraints(): array;
}
