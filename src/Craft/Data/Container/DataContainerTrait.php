<?php

namespace Craft\Data\Container;

use Craft\Data\Container\Exception\PropertyMissingException;
use Craft\Data\Container\Exception\PropertyNotValidException;
use Craft\Data\Container\Exception\PropertyValueNormalizationException;
use Craft\Data\Container\Exception\PropertyValueNotValidException;
use Craft\Data\Processor\StringProcessor;
use DateTimeInterface;

/**
 * Trait DataContainerTrait
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
trait DataContainerTrait
{
    /**
     * @var array Metadata describing the properties of the container and its types
     * @example:
     *   "code" => "integer|0",
     *   "name" => "string",
     *   "date_added" => "DateTime",
     *   "category" => "FullyQualifiedClassImplementingDataContainerInterface",
     *   "price" => "double",
     *   "products" => "array|null",
     */
    protected $metadata = [];

    /**
     * Default values store
     * @var array
     */
    protected $defaultValues = [];

    /**
     * @var array DataContainer state
     */
    protected $state = [];

    /**
     * @var string
     */
    protected $propertyNotValidErrorMessage = 'Property ["%s"] not valid for this class ["%s"]';

    /**
     * Support for public property getting
     *
     * @param string $property
     * @return mixed
     * @throws PropertyNotValidException
     */
    public function __get(string $property)
    {
        return $this->isValidProperty($property) && $this->hasPropertyApi($property, 'GET') ?
            $this->{$this->makePropertyApiMethodName($property, 'GET')}() :
            $this->getPropertyValue($property);
    }

    /**
     * Support for public property setting
     *
     * @param string $property
     * @param mixed $value Value of property
     * @return void
     * @throws PropertyValueNotValidException Thrown if property valuetype is inconsistent
     * @throws PropertyNotValidException Thrown if property is not defined in validProperties
     */
    public function __set(string $property, $value): void
    {
        $this->isValidProperty($property) && $this->hasPropertyApi($property, 'SET') ?
            $this->{$this->makePropertyApiMethodName($property, 'SET')}($value) :
            $this->addPropertyValue($property, $value);
    }

    /**
     * @inheritdoc
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->state[$name]);
    }

    /**
     * @inheritdoc
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->state[$name]);
    }

    /**
     * Define a property on data container
     * @param string $property
     * @param array $constraints
     * @param null $defaultValue
     */
    public function defineProperty(string $property, array $constraints = [], $defaultValue = null): void
    {
        $this->metadata[$property] = $constraints;

        if (!is_null($defaultValue)) {
            $this->defaultValues[$property] = $defaultValue;
        }
    }

    /**
     * Get the properties with associated metadata
     * @return array
     */
    public function getProperties(): array
    {
        return $this->metadata;
    }

    /**
     * Get the list of properties names
     * @return array
     */
    public function getPropertiesList(): array
    {
        return array_keys($this->metadata);
    }

    /**
     * Set a property value
     * @info Usage of $this->properties[$property] = $value / $this->properties[$property][] = $value
     * should be avoided, use addPropertyValue instead.
     * @param string $property
     * @param $propertyValue
     * @throws PropertyNotValidException
     */
    public function addPropertyValue(string $property, $propertyValue): void
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        // perform value type checking
        $this->checkTypes($property, $propertyValue);

        // add to array property type
        if (in_array('array', $this->getIntegritySpecification($property)) && !is_array($propertyValue)) {
            $this->state[$property][] = $propertyValue;
        } else {
            $this->state[$property] = $propertyValue;
        }
    }

    /**
     * Get a property value
     * @param string $property
     * @return mixed
     * @throws PropertyNotValidException
     */
    public function &getPropertyValue(string $property)
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        // default value when property was not set
        if (!array_key_exists($property, $this->state) && array_key_exists($property, $this->defaultValues)) {
            $propertyValue = &$this->defaultValues[$property];
        } elseif (array_key_exists($property, $this->state)) {
            $propertyValue = &$this->state[$property];
        }

        return $propertyValue;
    }

    /**
     * Get the DataContainer's current internal state as an array
     *
     * This method WILL NOT return default values or property values that HAD NOT BEEN explicitly populated.
     *
     * @return array [key => value] Data structure
     *
     * @see To get a normalized result set use ::normalizeData method
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->state as $property => $internalValue) {
            $value = $this->hasPropertyApi($property, 'GET') ?
                $this->{$this->makePropertyApiMethodName($property, 'GET')}() : $internalValue;
            $data[$property] = $this->normalizeValue($value, 'toArray');
        }

        return $data;
    }

    /**
     * Populate DataContainer from array and perform integrity checks
     * @param array $data
     * @param bool $skipExtraProperties Ignore extra properties that do not belong to the class
     * @param bool $skipMissingProperties Ignore missing properties in input $data
     * @throws PropertyMissingException
     * @throws PropertyNotValidException
     */
    public function fromArray(array $data, bool $skipExtraProperties = true, bool $skipMissingProperties = true): void
    {
        $extraProperties = array_keys(array_diff_key($data, $this->metadata));

        if (!$skipExtraProperties && count($extraProperties)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, implode(',', $extraProperties), get_class($this)));
        }

        foreach ($this->getPropertiesList() as $property) {

            $propertyIsPresent = array_key_exists($property, $data);

            if (!$propertyIsPresent && !$skipMissingProperties) {
                throw new PropertyMissingException(sprintf("Property [%s] is missing from input array when using %s->fromArray", $property, get_class($this)));
            } elseif ($propertyIsPresent) {
                $this->addPropertyValue($property, $data[$property]);
            }
        }

    }

    /**
     * Normalize all properties of this container:
     * - handles default value resolution
     * - handles DataContainerInterface property values normalization
     * - return simple array with key->value OR multidimensional array with key->array but never object values
     *
     * @return array
     */
    public function normalizeData(): array
    {
        $data = [];

        foreach ($this->getPropertiesList() as $property) {
            $value = $this->hasPropertyApi($property, 'GET') ?
                $this->{$this->makePropertyApiMethodName($property, 'GET')}() :
                $this->getPropertyValue($property);
            $data[$property] = $this->normalizeValue($value, 'normalizeData');
        }

        return $data;
    }

    /**
     * Check if a property is valid
     * @param string $property
     * @return bool
     */
    public function isValidProperty(string $property): bool
    {
        if (!empty($this->metadata) && !array_key_exists($property, $this->metadata)) {
            return false;
        }

        return true;
    }

    /**
     * Set custom $errorMessage for PropertyNotValidException
     * @param string $errorMessage
     * @see There are 2 string parameters that will be replaced in the message using sprintf
     * first is the invalid $property and the second is the current class name
     * @template 'Property ["%s"] not valid for this class ["%s"]'
     */
    public function setPropertyNotValidErrorMessage(string $errorMessage)
    {
        $this->propertyNotValidErrorMessage = $errorMessage;
    }

    /**
     * Normalize a property value
     * @param $value
     * @param string $contextNormalizer
     * @return array|string
     * @throws PropertyValueNormalizationException
     */
    private function normalizeValue($value, string $contextNormalizer)
    {
        $normalizedValue = $value;

        // object value
        if (is_object($value)) {
            if ($value instanceof DateTimeInterface) {
                // handle DateTime value objects
                $normalizedValue = $value->format("Y-m-d\TH:i:s.u\Z");
            } elseif ($value instanceof DataContainerInterface || method_exists($value, $contextNormalizer)) {
                // handle DataContainer value objects
                $normalizedValue = $value->{$contextNormalizer}();
            } elseif (method_exists($value, 'toString')) {
                // handle other value objects which have a `toString` method
                $normalizedValue = $value->toString();
            } else {
                throw new PropertyValueNormalizationException(sprintf(
                        "Value error: object of class [%s] does not implement DataContainerInterface OR does not have a [$contextNormalizer] method",
                        get_class($value))
                );
            }
        } elseif (is_array($value)) {
            foreach ($value as $subProperty => $subPropertyValue) {
                $normalizedValue[$subProperty] = $this->normalizeValue($subPropertyValue, $contextNormalizer);
            }
        }

        return $normalizedValue;
    }

    /**
     * Check if a property has a predefined accessor/mutator
     * @param string $property
     * @param string $context
     * @return bool
     */
    private function hasPropertyApi(string $property, string $context): bool
    {
        return method_exists($this, $this->makePropertyApiMethodName($property, $context));
    }

    /**
     * Make a property api method name
     * @param string $property
     * @param string $context
     * @return string
     */
    private function makePropertyApiMethodName(string $property, string $context): string
    {
        $propertyApi = 'set' . StringProcessor::camelize($property);

        if ($context === 'GET') {
            $propertyApi = 'get' . StringProcessor::camelize($property);
        }

        return $propertyApi;
    }

    /**
     * Get defined integrity check type|value for a property
     * @param string $property
     * @return array
     */
    private function getIntegritySpecification(string $property)
    {
        $allConstraints = $this->metadata[$property];

        if (!is_array($allConstraints)) {
            $constraints = $this->parseIntegritySpecification($allConstraints);
        } else {
            $constraints = [];
            // extract only the `type` related constraints
            foreach ($allConstraints as $key => $value) {
                if (is_numeric($key)) {
                    $constraints[] = $value;
                }
            }
        }

        return $constraints;
    }

    /**
     * Parse integrity specification "array|null"
     * @param string $integritySpecification
     * @return array
     */
    private function parseIntegritySpecification(string $integritySpecification)
    {
        $parsedIntegritySpecification = [];

        if (strpos($integritySpecification, '|') !== false) {
            $parsedIntegritySpecification = explode('|', $integritySpecification);
        } elseif (!empty($integritySpecification)) {
            $parsedIntegritySpecification = [$integritySpecification];
        }

        return $parsedIntegritySpecification;
    }

    /**
     * Check property value type
     * @param string $property
     * @param mixed $value
     * @return bool
     * @throws PropertyValueNotValidException
     */
    private function checkTypes(string $property, $value)
    {
        $integrityConstraints = $this->getIntegritySpecification($property);

        $actualValueType = gettype($value);

        if ($actualValueType === 'double') {
            $actualValueType = is_int($value) ? 'integer' : 'number';
        } else {
            if ($actualValueType === 'NULL') {
                $actualValueType = 'null';
            }
        }

        if (count($integrityConstraints)) {
            // object check
            if ($actualValueType === 'object') {
                if (!in_array(get_class($value), $integrityConstraints)) {
                    throw new PropertyValueNotValidException(
                        sprintf("Type error(%s): Property [%s] accepts only [%s] values, but given value is instance of [%s]",
                            get_class($this),
                            $property,
                            implode(',', $integrityConstraints),
                            get_class($value)
                        ));
                }
            } else {
                // primitive type value
                $isValidType = false;
                foreach ($integrityConstraints as $type) {
                    if ($type === 'object' && is_object($value)) {
                        $isValidType = true;
                        break;
                    } elseif ($type === 'array' && is_array($value)) {
                        $isValidType = true;
                        break;
                    } elseif ($type === 'string' && is_string($value)) {
                        $isValidType = true;
                        break;
                    } elseif ($type === 'number' && is_numeric($value)) {
                        $isValidType = true;
                        break;
                    } elseif (($type === 'integer' || $type === 'int') && is_int($value)) {
                        $isValidType = true;
                        break;
                    } elseif (($type === 'boolean' || $type === 'bool') && is_bool($value)) {
                        $isValidType = true;
                        break;
                    } elseif ($type === 'null' && $value === null) {
                        $isValidType = true;
                        break;
                    }
                }
                if (!$isValidType) {
                    throw new PropertyValueNotValidException(
                        sprintf("Type error(%s): Property [%s] accepts only [%s] values, but given value is [%s]",
                            get_class($this),
                            $property,
                            implode(',', $integrityConstraints),
                            $actualValueType
                        ));
                }
            }
        }

        return true;
    }

}
