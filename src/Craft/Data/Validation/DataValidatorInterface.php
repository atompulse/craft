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
    public function getValidatorConstraints(): array;
}
