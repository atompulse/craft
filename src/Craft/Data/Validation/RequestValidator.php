<?php

namespace Craft\Data\Validation;

use Craft\Messaging\RequestInterface;

/**
 * Class RequestValidator
 * @package Craft\Data\Validation
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class RequestValidator implements RequestValidatorInterface
{
    /**
     * @var ArrayValidatorInterface
     */
    protected $validator;

    public function __construct(ArrayValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param RequestInterface $object
     * @return array
     */
    public function validate(RequestInterface $request): array
    {
        return $this->validator->validate($request->normalizeData(), $request->getValidatorConstraints());
    }
}
