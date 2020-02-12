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
     * @inheritdoc
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
     * @inheritdoc
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
                    } elseif (($type === 'integer' || $type == 'int') && is_int($value)) {
                        $isValidType = true;
                        break;
                    } elseif (($type === 'boolean' || $type == 'bool') && is_bool($value)) {
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
     * Transform data properties into PHP-array structure keeping
     * property items in their respective DataContainerInterface state if the values are objects
     *
     * This method will ONLY return the current state of the data with object and primitives,
     * it will not return default values or property values that have not been set.
     *
     * @param string|null $property
     * @return array [key => value] Data structure
     * @throws PropertyNotValidException
     * @throws PropertyValueNotValidException
     * @see To get a normalized result set use ::normalizeData method
     *
     */
    public function toArray(string $property = null): array
    {
        $properties = $this->properties;

        if (!is_null($property)) {
            if ($this->isValidProperty($property)) {
                if (is_array($this->properties[$property]) ||
                    $this->properties[$property] instanceof DataContainerInterface ||
                    method_exists($this->properties[$property], 'toArray')) {
                    $properties = $this->properties[$property];
                } else {
                    throw new PropertyValueNotValidException(sprintf(
                            "Property type error: property [%s] does not implement DataContainerInterface OR does not have a toArray method OR is not an array",
                            $property)
                    );
                }
            } else {
                throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
            }
        }

        return array_map(
            function ($item) {
                if (is_object($item)) {
                    // default object->primitive type conversion for DateTime objects
                    if ($item instanceof DateTimeInterface) {
                        return $item->format("Y-m-d\TH:i:s.u\Z");
                    }
                    if ($item instanceof DataContainerInterface || method_exists($item, 'toArray')) {
                        return $item->toArray();
                    } else {
                        throw new PropertyValueNotValidException(sprintf(
                                "Value type error: class [%s] does not implement DataContainerInterface OR does not have a toArray method",
                                get_class($item))

                        );
                    }
                } elseif (is_array($item)) {
                    return array_map(
                        function ($subItem) {
                            if ($subItem instanceof DataContainerInterface || method_exists($subItem, 'toArray')) {
                                return $subItem->toArray();
                            }
                            return $subItem;
                        },
                        $item
                    );
                }
                return $item;
            },
            $properties
        );
    }

    /**
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

        foreach ($this->validProperties as $property => $integritySpecification) {
            if (!array_key_exists($property, $data) && !$skipMissingProperties) {
                throw new PropertyMissingException(sprintf("Property [%s] is missing from input array when using %s->fromArray", $property, get_class($this)));
            } elseif (array_key_exists($property, $data)) {
                $this->$property = $data[$property];
            }
        }

    }

    /**
     * Normalize all properties of this container:
     * - handles default value resolution
     * - handles DataContainerInterface property values normalization
     * - return simple array with key->value OR multidimensional array with key->array but never object values
     *
     * @param string|null $property Normalize a specific property of the container
     * @return array
     * @throws PropertyNotValidException
     */
    public function normalizeData(string $property = null): array
    {
        $data = [];

        $validProperties = $this->validProperties;

        if (!is_null($property)) {
            if ($this->isValidProperty($property)) {
                return $this->normalizeValue($this->$property);
            } else {
                throw new PropertyNotValidException(sprintf($this->propertyNotValidErrorMessage, $property, get_class($this)));
            }
        }

        foreach ($validProperties as $validProperty => $types) {
            $data[$validProperty] = $this->normalizeValue($this->$validProperty);
        }

        return $data;
    }

    /**
     * Normalize a property value
     * @param $value
     * @return mixed
     * @throws PropertyValueNormalizationException
     */
    protected function normalizeValue($value)
    {
        $normalizedValue = $value;
        // object value
        if (is_object($value)) {
            if ($value instanceof DateTimeInterface) {
                $normalizedValue = $value->format("Y-m-d\TH:i:s.u\Z");
            } elseif ($value instanceof DataContainerInterface || method_exists($value, 'normalizeData')) {
                $normalizedValue = $value->normalizeData();
            } else {
                throw new PropertyValueNormalizationException(sprintf(
                        "Value error: object of class [%s] does not implement DataContainerInterface OR does not have a [normalizeData] method",
                        get_class($value))
                );
            }
        } elseif (is_array($value)) {
            foreach ($value as $arrProperty => $arrValue) {
                $normalizedValue[$arrProperty] = $this->normalizeValue($arrValue);
            }
        }

        return $normalizedValue;
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

}
