<?php

namespace Craft\Data\Validation;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Processor\StringProcessor;
use DateTime;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Validator\Constraints as Assert;
use Craft\Data\Validation\Constraints as CraftAssert;

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
                case DateTime::class :
                    $validatorConstraints[] = new CraftAssert\DateTime();
                    break;
                case 'null' :
                    $validatorConstraints[] = new CraftAssert\NotEmpty(['allowNull' => true]);
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
                            throw new LogicException(
                                sprintf(
                                    "[%s] MUST implement [%s] and [%s] to be supported by the MetadataConstraintsBuilder, declared for property [%s] of %s",
                                    $constraintName,
                                    DataContainerInterface::class,
                                    DataValidatorInterface::class,
                                    $property,
                                    get_class($this)
                                )
                            );
                        }
                    } else {

                        // try to determine if its a valid symfony validator class
                        $symfonyConstraintClass = '\Symfony\Component\Validator\Constraints\\' . $this->buildValidatorConstraintName($constraintName);
                        $craftConstraintClass = '\Craft\Data\Validation\Constraints\\' . $this->buildValidatorConstraintName($constraintName);

                        if (class_exists($symfonyConstraintClass)) {
                            $validatorConstraints[] = $this->buildValidator($symfonyConstraintClass, $hasArguments, $args);
                        } elseif (class_exists($craftConstraintClass)) {
                            $validatorConstraints[] = $this->buildValidator($craftConstraintClass, $hasArguments, $args);
                        } else {
                            throw new LogicException(
                                sprintf(
                                    "[%s] is not a valid Symfony or Craft Validator Constraint, declared for property [%s] of %s",
                                    $constraintName,
                                    $property,
                                    get_class($this)
                                )
                            );
                        }
                    }

                    break;
            }
        }

        // add "not empty" validator for non-nullable properties
        if ($isNullable === false) {
            $validatorConstraints[] = new CraftAssert\NotEmpty();
        }

        return $validatorConstraints;
    }

    /**
     * @param string $rawName
     * @return string
     */
    protected function buildValidatorConstraintName(string $rawName): string
    {
        return StringProcessor::classify($rawName);
    }

    /**
     * @param string $constraintClass
     * @param bool $hasArguments
     * @param array|null $args
     * @return object
     * @throws ReflectionException
     */
    protected function buildValidator(string $constraintClass, bool $hasArguments, $args = null): object
    {
        $factory = new ReflectionClass($constraintClass);

        if ($hasArguments) {
            return $factory->newInstance($args);
        }

        return $factory->newInstance();
    }
}
