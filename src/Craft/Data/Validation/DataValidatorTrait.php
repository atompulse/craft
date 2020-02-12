<?php

namespace Craft\Data\Validation;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait DataValidatorTrait
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
trait DataValidatorTrait
{
    public function getValidatorConstraints(): array
    {
        if (method_exists($this, 'getProperties')) {
            $validatorConstraints = [];

            $metadata = $this->getProperties();

            foreach ($metadata as $propertyName => $constraints) {
                $validatorConstraints[$propertyName] = $this->translateConstraints($constraints);
            }

            return $validatorConstraints;
        }

        throw new \LogicException("DataValidatorTrait MUST be attached on DataContainerInterface classes only");

    }

    private function translateConstraints(array $constraints)
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
                case 'null' :
                    $validatorConstraints[] = new Assert\NotBlank(['allowNull' => true]);
                    $nullable = true;
                    break;
                default:

                    $refinedName = $this->buildConstraintName($constraintName);

                        // try to determine if its a symfony validator before giving up
                        $constraintClass = '\Symfony\Component\Validator\Constraints\\' . $this->buildConstraintName($constraintName);

                        $factory = new \ReflectionClass($constraintClass);
                        if ($hasArguments) {
                            $constraintInstance = $factory->newInstance($args);
                        } else {
                            $constraintInstance = $factory->newInstance();
                        }

                        $validatorConstraints[] = $constraintInstance;


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