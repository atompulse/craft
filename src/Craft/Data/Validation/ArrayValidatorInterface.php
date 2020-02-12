<?php

namespace Craft\Data\Validation;

/**
 * Interface ArrayValidatorInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface ArrayValidatorInterface
{
    public function validate(array $data, array $constraints): array;
}
