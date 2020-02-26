<?php

namespace Craft\Data\Validation;

use Craft\Data\Container\DataContainerInterface;
use LogicException;
use ReflectionClass;

/**
 * Class StructuredDataValidator
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class StructuredDataValidator implements StructuredDataValidatorInterface
{
    /**
     * @var RecursiveValidatorInterface
     */
    protected $validator;

    /**
     * StructuredDataValidator constructor.
     * @param RecursiveValidatorInterface $validator
     */
    public function __construct(RecursiveValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Will return a list of properties obtained from the $structureClass::getValidatorConstraints(), with values as array of ContextualError items
     * @param array $data
     * @param string $structureClass
     * @return array
     */
    public function validate(array $data, string $structureClass): array
    {
        if (class_exists($structureClass) &&
            is_subclass_of($structureClass, DataContainerInterface::class) &&
            is_subclass_of($structureClass, DataValidatorInterface::class)) {

            $constraints = (new ReflectionClass($structureClass))->newInstance()->getValidatorConstraints();

            return $this->validator->validate($data, $constraints);
        }

        throw new LogicException(sprintf("[%s] MUST implement [%s] and [%s] to be supported by the StructuredDataValidator",
            $structureClass, DataContainerInterface::class, DataValidatorInterface::class));
    }
}
