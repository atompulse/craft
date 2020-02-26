<?php

namespace Craft\Data\Validation;

use Craft\Exception\ContextualError;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RecursiveValidator
 *
 * A symfony constraints validator capable of handling N-dimensional arrays of constraint validators
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class RecursiveValidator implements RecursiveValidatorInterface
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
    public function validate(array $data, array $constraints, string $context = null): array
    {
        $errors = [];

        foreach ($constraints as $property => $propertyConstraints) {

            $errorContext = $context ? $context . '.' . $property : $property;

            $propertyIsPresent = array_key_exists($property, $data);
            $value = $propertyIsPresent ? $data[$property] : null;

            if ($propertyIsPresent === false) {
                $errors[$property][] = new ContextualError('This property is required.', $errorContext);
            } else {
                // sub array with constraints
                if (is_array($propertyConstraints[0])) {
                    // $value should be also an array
                    if (is_array($value)) {
                        $subErrors = $this->validate($value, $propertyConstraints[0], $property);
                        // flatten the $propertyName's $subErrors
                        foreach ($subErrors as $subPropertyName => $serviceErrors) {
                            $errors[$property . '.' . $subPropertyName] = $serviceErrors;
                        }
                    } else {
                        $errors[$property][] = new ContextualError('This value should be an array data structure.', $property);
                    }
                } else {
                    $validationErrors = $this->validator->validate($value, $propertyConstraints);

                    /** @var \Symfony\Component\Validator\ConstraintViolation $error */
                    foreach ($validationErrors as $error) {
                        $errors[$property][] = new ContextualError($error->getMessage(), $errorContext);
                    }
                }
            }
        }

        return $errors;
    }
}
