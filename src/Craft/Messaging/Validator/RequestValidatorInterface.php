<?php

namespace Craft\Messaging\Validator;

use Craft\Messaging\RequestInterface;

/**
 * Interface RequestValidatorInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface RequestValidatorInterface
{
    /**
     * @param RequestInterface $request
     * @return array
     */
    public function validate(RequestInterface $request): array;
}
