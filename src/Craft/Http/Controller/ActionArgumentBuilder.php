<?php

namespace Craft\Http\Controller;

use Craft\Data\Processor\StringProcessor;
use Craft\Data\Validation\ArrayValidatorInterface;
use Craft\Data\Validation\RequestValidatorInterface;
use Craft\Http\Controller\Exception\ActionArgumentException;
use Craft\Messaging\RequestInterface;
use Craft\Messaging\Service\ServiceStatusCodes;
use Craft\Meta\GetClassSetters;
use ReflectionClass;


/**
 * Class ActionArgumentBuilder
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ActionArgumentBuilder implements ActionArgumentBuilderInterface
{
    /**
     * @var RequestValidatorInterface
     */
    private $validator;

    public function __construct(ArrayValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $inputData
     * @param string $inputClass
     * @return RequestInterface
     * @throws ActionArgumentException
     * @throws \Throwable
     */
    public function build(array $inputData, string $inputClass): RequestInterface
    {
        /** @var RequestInterface $argument */
        $argument = (new ReflectionClass($inputClass))->newInstance();

        $errors = $this->validator->validate($inputData, $argument->getValidatorConstraints());

        if (count($errors)) {
            $err = new ActionArgumentException(ServiceStatusCodes::INVALID_INPUT);
            foreach ($errors as $property => $propertyErrors) {
                foreach ($propertyErrors as $error) {
                    $err->addError($error);
                }
            }
            throw $err;
        }

        $this->populate($argument, $inputData);

        return $argument;
    }

    /**
     * Populate the data container object
     * @param RequestInterface $object
     * @param array $inputData
     */
    protected function populate(RequestInterface $object, array $inputData)
    {
        $props = $object->getProperties();
        $setters = (new GetClassSetters())(get_class($object));

        foreach ($props as $prop => $integrityConstraints) {
            if (array_key_exists($prop, $inputData)) {
                // type MUST be the first item
                $propType = $integrityConstraints[0];
                // container property api
                $setter = 'set' . StringProcessor::camelize($prop);
                // resolve value
                $value = $this->resolvePrimitiveValue($inputData[$prop], $propType);

                if (in_array($setter, $setters)) {
                    // if property api present then it has priority
                    $object->$setter($value);
                } else {
                    // use standard api
                    $object->addPropertyValue($prop, $value);
                }
            }
        }
    }

    /**
     * Convert string to correct primitive type
     * @param $stringValue
     * @param string $propType
     * @return bool|float|int|string|array
     */
    protected function resolvePrimitiveValue($stringValue, string $propType)
    {
        $typedValue = $stringValue;

        if ($propType !== 'string') {
            if (is_numeric($stringValue)) {
                if (is_float($stringValue) || is_double($stringValue)) {
                    $typedValue = floatval($stringValue);
                } else {
                    $typedValue = intval($stringValue);
                }
            } elseif (is_bool($stringValue)) {
                $typedValue = boolval($stringValue);
            }
        }

        return $typedValue;
    }
}
