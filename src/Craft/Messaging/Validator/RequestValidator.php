<?php

namespace Craft\Messaging\Validator;

use Craft\Data\Validation\StructuredDataValidatorInterface;
use Craft\Messaging\RequestInterface;

/**
 * Class RequestValidator
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class RequestValidator implements RequestValidatorInterface
{
    /**
     * @var StructuredDataValidatorInterface
     */
    protected $validator;

    /**
     * RequestValidator constructor.
     * @param StructuredDataValidatorInterface $validator
     */
    public function __construct(StructuredDataValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param RequestInterface $object
     * @return array
     */
    public function validate(RequestInterface $request): array
    {
        return $this->validator->validate($request->normalizeData(), get_class($request));
    }

}
