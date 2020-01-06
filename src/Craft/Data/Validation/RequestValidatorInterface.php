<?php

namespace Craft\Data\Validation;

use Craft\Messaging\RequestInterface;

/**
 * Interface RequestValidatorInterface
 * @package Craft\Data\Validation
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface RequestValidatorInterface
{
    public function validate(RequestInterface $object): array;
}
