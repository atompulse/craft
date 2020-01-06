<?php

namespace Craft\Data\Validation;

use Craft\Messaging\RequestInterface;
use Craft\Messaging\Service\ServiceError;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function validate(RequestInterface $object): array
    {
        return $this->validator->validate($object->normalizeData(), $object->getValidatorConstraints());
    }
}
