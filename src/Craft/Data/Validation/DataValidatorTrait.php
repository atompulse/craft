<?php

namespace Craft\Data\Validation;

use Doctrine\Common\Inflector\Inflector;

/**
 * Trait DataValidatorTrait
 * @package Craft\Data\Validation
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
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
        foreach ($constraints as $constraint => $args) {

            if (is_array($args)) {
                $constraintName = $constraint;
                $hasArguments = true;
            } else {
                $constraintName = $args;
                $hasArguments = false;
            }

            $nullable = false;

            switch ($constraintName) {
                case 'string' :
                    $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\Type(['type' => 'string']);
                    break;
                case 'int' :
                case 'integer' :
                case 'number' :
                    $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\Type(['type' => 'numeric']);
                    break;
                case 'object' :
                    $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\Type(['type' => 'object']);
                    break;
                case 'bool' :
                    $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\Type(['type' => 'bool']);
                    break;
                case 'null' :
                    $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\NotBlank(['allowNull' => true]);
                    $nullable = true;
                    break;
                default:
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

            // add not blank validator for all fields without 'null'
            if (!$nullable) {
                $validatorConstraints[] = new \Symfony\Component\Validator\Constraints\NotBlank([]);
            }

        }

        return $validatorConstraints;
    }

    protected function buildConstraintName(string $rawName): string
    {
        return Inflector::classify($rawName);
    }

}