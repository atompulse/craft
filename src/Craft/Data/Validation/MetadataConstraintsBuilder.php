<?php

namespace Craft\Data\Validation;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Processor\StringProcessor;
use LogicException;
use ReflectionClass;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MetadataConstraintsBuilder
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class MetadataConstraintsBuilder
{
    /**
     * @param string $property
     * @param array $metadata
     * @return array
     */
    public function build(string $property, array $metadata): array
    {
        $validatorConstraints = [];
        $isNullable = false;

        foreach ($metadata as $constraint => $args) {

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
                case 'float' :
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
                    $isNullable = true;
                    break;
                default:

                    if (class_exists($constraintName)) {
                        // for DataContainerInterface/DataValidatorInterface class specification obtain
                        // the constraints by calling DataValidatorInterface::getValidatorConstraints()
                        $implementedInterfaces = class_implements($constraintName);

                        if (array_key_exists(DataContainerInterface::class, $implementedInterfaces) &&
                            array_key_exists(DataValidatorInterface::class, $implementedInterfaces)) {

                            /** @var DataValidatorInterface $argument */
                            $argument = (new ReflectionClass($constraintName))->newInstance();

                            $validatorConstraints[] = $argument->getValidatorConstraints();

                        } else {
                            throw new LogicException(sprintf(
                                "[%s] MUST implement [%s] and [%s] to be supported by the MetadataConstraintsBuilder, declared for property [%s] of %s",
                                $constraintName, DataContainerInterface::class, DataValidatorInterface::class, $property, get_class($this)));
                        }
                    } else {

                        // try to determine if its a valid symfony validator class
                        $constraintClass = '\Symfony\Component\Validator\Constraints\\' . $this->buildSymfonyValidatorConstraintName($constraintName);

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
                                $constraintName, $property, get_class($this)));
                        }
                    }

                    break;
            }
        }

        // add "not blank" validator for non-nullable properties
        if ($isNullable === false) {
            $validatorConstraints[] = new Assert\NotBlank();
        }

        return $validatorConstraints;
    }

    protected function buildSymfonyValidatorConstraintName(string $rawName): string
    {
        return StringProcessor::classify($rawName);
    }
}
