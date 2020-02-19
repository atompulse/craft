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
     * @var array Store to describe the properties of the container and its types
     * @example:
     *   "code" => "integer|0",
     *   "name" => "string",
     *   "date_added" => "DateTime",
     *   "category" => "FullyQualifiedClassImplementingDataContainerInterface",
     *   "price" => "double",
     *   "products" => "array|null",
     */
    protected $validProperties = [];

    /**
     * Default values store
     * @var array
     */
    protected $defaultValues = [];

    /**
     * @var array Store values of all valid properties.
     */
    protected $properties = [];

    /**
     * @var string
     */
    private $propertyNotValidErrorMessage = 'Property ["%s"] not valid for this class ["%s"]';

    /**
     * Support for public property getting
     * @param string $property
     * @return mixed
     * @throws PropertyNotValidException
     */
    public function &__get(string $property)
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        $propertyValue = null;

        // specialized getter method
        $getterMethod = "get" . StringProcessor::camelize($property);

        if (method_exists($this, $getterMethod)) {
            $propertyValue = $this->$getterMethod();
        } else {
            // default value when property was not set
            if (!array_key_exists($property, $this->properties) && array_key_exists($property, $this->defaultValues)) {
                $propertyValue = &$this->defaultValues[$property];
            } elseif (array_key_exists($property, $this->properties)) {
                $propertyValue = &$this->properties[$property];
            }
        }

        return $propertyValue;
    }

    /**
     * Support for public property setting
     * @param string $property
     * @param mixed $value Value of property
     * @return void
     * @throws PropertyValueNotValidException Thrown if property valuetype is inconsistent
     * @throws PropertyNotValidException Thrown if property is not defined in validProperties
     */
    public function __set(string $property, $value)
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        // Check if there's a specialized setter method
        $setterMethod = "set" . StringProcessor::camelize($property);
        if (method_exists($this, $setterMethod)) {
            $this->$setterMethod($value);
            // if custom setter did not "initialized" the property then
            // by default we assume the user "left" the property as null
            if (!array_key_exists($property, $this->properties)) {
                $this->properties[$property] = null;
            }
        } else {
            $integrityConstraints = $this->getIntegritySpecification($property);
            // add to array property type
            if (in_array('array', $integrityConstraints) && !is_array($value)) {
                $this->properties[$property][] = $value;
            } else {
                $this->properties[$property] = $value;
            }
        }

        // perform property value type checking
        // performed last in order to be able to test the validity
        // of property values that had been set using a custom setter
        $this->checkTypes($property, $this->properties[$property]);
    }

    /**
     * @inheritdoc
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @inheritdoc
     * @param $name
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * Define a property on data container
     * @param string $property
     * @param array $constraints
     * @param null $defaultValue
     */
    public function defineProperty(string $property, array $constraints = [], $defaultValue = null): void
    {
        $this->validProperties[$property] = $constraints;

        if (!is_null($defaultValue)) {
            $this->defaultValues[$property] = $defaultValue;
        }
    }

    /**
     * Get the defined list of properties
     * @return array
     */
    public function getProperties(): array
    {
        return $this->validProperties;
    }

    /**
     * Get the list of properties names
     * @return array
     */
    public function getPropertiesList(): array
    {
        return array_keys($this->validProperties);
    }

    /**
     * Set a property value
     * @info Usage of $this->properties[$property] = $value / $this->properties[$property][] = $value
     * should be avoided, use addPropertyValue instead.
     * @param string $property
     * @param $value
     * @throws PropertyNotValidException
     */
    public function addPropertyValue(string $property, $value): void
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        $integrityConstraints = $this->getIntegritySpecification($property);
        // add to array property type
        if (in_array('array', $integrityConstraints) && !is_array($value)) {
            $this->properties[$property][] = $value;
        } else {
            $this->properties[$property] = $value;
        }

        // perform property value type checking
        $this->checkTypes($property, $this->properties[$property]);
    }

    /**
     * Get a property value
     * @param string $property
     * @return mixed
     * @throws PropertyNotValidException
     */
    public function getPropertyValue(string $property)
    {
        if (!$this->isValidProperty($property)) {
            throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
        }

        $propertyValue = null;

        // default value when property was not set
        if (!array_key_exists($property, $this->properties) && array_key_exists($property, $this->defaultValues)) {
            $propertyValue = &$this->defaultValues[$property];
        } elseif (array_key_exists($property, $this->properties)) {
            $propertyValue = &$this->properties[$property];
        }

        return $propertyValue;
    }

    /**
     * Get the DataContainer's current state as an array
     *
     * This method WILL NOT return default values or property values that have not been explicitly added.
     *
     * @return array [key => value] Data structure
     *
     * @see To get a normalized result set use ::normalizeData method
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->properties as $property => $value) {
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
        $extraProperties = array_keys(array_diff_key($data, $this->validProperties));

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
            $data[$property] = $this->normalizeValue($this->getPropertyValue($property), 'normalizeData');
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
        if (!empty($this->validProperties) && !array_key_exists($property, $this->validProperties)) {
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
     * @param string $contextCall
     * @return array|string
     * @throws PropertyValueNormalizationException
     */
    private function normalizeValue($value, string $contextCall)
    {
        $normalizedValue = $value;

        // object value
        if (is_object($value)) {
            if ($value instanceof DateTimeInterface) {
                // handle DateTime value objects
                $normalizedValue = $value->format("Y-m-d\TH:i:s.u\Z");
            } elseif ($value instanceof DataContainerInterface || method_exists($value, $contextCall)) {
                // handle DataContainer value objects
                $normalizedValue = $value->normalizeData();
            } elseif (method_exists($value, 'toString')) {
                // handle other value objects which have a `toString` method
                $normalizedValue = $value->toString();
            } else {
                throw new PropertyValueNormalizationException(sprintf(
                        "Value error: object of class [%s] does not implement DataContainerInterface OR does not have a [$contextCall] method",
                        get_class($value))
                );
            }
        } elseif (is_array($value)) {
            foreach ($value as $subProperty => $subPropertyValue) {
                $normalizedValue[$subProperty] = $this->normalizeValue($subPropertyValue, $contextCall);
            }
        }

        return $normalizedValue;
    }

    /**
     * Get defined integrity check type|value for a property
     * @param string $property
     * @return array
     */
    private function getIntegritySpecification(string $property)
    {
        $allConstraints = $this->validProperties[$property];

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
                            $value
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
