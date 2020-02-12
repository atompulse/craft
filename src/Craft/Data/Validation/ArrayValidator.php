<?php

namespace Craft\Data\Validation;

use Craft\Messaging\Service\ServiceError;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ArrayValidator
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ArrayValidator implements ArrayValidatorInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     * @param array $constraints
     * @return array
     */
    public function validate(array $data, array $constraints): array
    {
        $errors = [];

        $validatorConstraints = $constraints;

        foreach ($validatorConstraints as $propertyName => $propertyConstraints) {

            $value = $data[$propertyName] ?? null;

            $validationErrors = $this->validator->validate($value, $propertyConstraints);
            /** @var \Symfony\Component\Validator\ConstraintViolation $error */
            foreach ($validationErrors as $error) {
                $errors[$propertyName][] = new ServiceError($error->getMessage(), $propertyName);
            }
        }

        return $errors;
    }
}
