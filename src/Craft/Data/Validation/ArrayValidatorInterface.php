<?php

namespace Craft\Data\Validation;

/**
 * Interface ArrayValidatorInterface
 * @package Craft\Data\Validation
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface ArrayValidatorInterface
{
    public function validate(array $data, array $constraints): array;
}
