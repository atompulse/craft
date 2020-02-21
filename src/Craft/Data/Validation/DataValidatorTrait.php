<?php

namespace Craft\Data\Validation;

use Doctrine\Common\Inflector\Inflector;
use LogicException;
use ReflectionClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait DataValidatorTrait
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
trait DataValidatorTrait
{
    /**
     * @return array
     */
    public function getValidatorConstraints(): array
    {
        if (method_exists($this, 'getProperties')) {
            $validatorConstraints = [];

            $metadata = $this->getProperties();

            foreach ($metadata as $propertyName => $constraints) {
                $validatorConstraints[$propertyName] = $this->translateConstraints($propertyName, $constraints);
            }

            return $validatorConstraints;
        }

        throw new LogicException("DataValidatorTrait MUST be attached on DataContainerInterface classes only");

    }

    /**
     * @param array $constraints
     * @return array
     */
    private function translateConstraints(string $propertyName, array $constraints)
    {
        $validatorConstraints = [];
        $nullable = false;

        foreach ($constraints as $constraint => $args) {

            if (is_array($args)) {
                $constraintName = $constraint;
                $hasArguments = true;
            } else {
                $constraintName = $args;
                $hasArguments = false;
            }

            switch ($constraintName) {
                case 'string' :
                    $validatorConstraints[] = new Assert\Type(['type' => 'string']);
                    break;
                case 'int' :
                case 'integer' :
                case 'number' :
                    $validatorConstraints[] = new Assert\Type(['type' => 'numeric']);
                    break;
                case 'object' :
                    $validatorConstraints[] = new Assert\Type(['type' => 'object']);
                    break;
                case 'bool' :
                    $validatorConstraints[] = new Assert\Type(['type' => 'bool']);
                    break;
                case 'array' :
                    $validatorConstraints[] = new Assert\Type(['type' => 'array']);
                    break;
                case 'null' :
                    $validatorConstraints[] = new Assert\NotBlank(['allowNull' => true]);
                    $nullable = true;
                    break;
                default:

                    $refinedName = $this->buildConstraintName($constraintName);

                    // try to determine if its a symfony validator before giving up
                    $constraintClass = '\Symfony\Component\Validator\Constraints\\' . $this->buildConstraintName($constraintName);

                    if (class_exists($constraintClass)) {

                        $factory = new ReflectionClass($constraintClass);

                        if ($hasArguments) {
                            $constraintInstance = $factory->newInstance($args);
                        } else {
                            $constraintInstance = $factory->newInstance();
                        }

                        $validatorConstraints[] = $constraintInstance;
                    } else {
                        throw new LogicException(sprintf(
                            "[%s] is not a valid Symfony Validator Constraint, declared for property [%s] of %s",
                            $constraintName, $propertyName, get_class($this)));
                    }

                    break;
            }
        }

        // add "not blank" validator for non 'nullable' properties
        if (!$nullable) {
            $validatorConstraints[] = new Assert\NotBlank();
        }

        return $validatorConstraints;
    }

    protected function buildConstraintName(string $rawName): string
    {
        return Inflector::classify($rawName);
    }

}
