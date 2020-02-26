<?php

namespace Craft\Http\Controller;

use Craft\Data\Processor\StringProcessor;
use Craft\Data\Validation\StructuredDataValidatorInterface;
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
     * @var StructuredDataValidatorInterface
     */
    private $validator;

    public function __construct(StructuredDataValidatorInterface $validator)
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
        $errors = $this->validator->validate($inputData, $inputClass);

        if (count($errors)) {
            $err = new ActionArgumentException(ServiceStatusCodes::INVALID_INPUT);
            foreach ($errors as $property => $propertyErrors) {
                foreach ($propertyErrors as $error) {
                    $err->addError($error);
                }
            }
            // stop execution
            throw $err;
        }

        return $this->populate($inputClass, $inputData);
    }

    /**
     * @param string $inputClass
     * @param array $inputData
     * @return RequestInterface
     */
    protected function populate(string $inputClass, array $inputData): RequestInterface
    {
        /** @var RequestInterface $object */
        $object = (new ReflectionClass($inputClass))->newInstance();

        $props = $object->getProperties();
        $setters = (new GetClassSetters())($inputClass);

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

        return $object;
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
