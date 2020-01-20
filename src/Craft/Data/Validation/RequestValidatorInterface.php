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
    /**
     * @param RequestInterface $request
     * @return array
     */
    public function validate(RequestInterface $request): array;
}
