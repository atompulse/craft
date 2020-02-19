<?php

namespace Craft\Data\Container;

/**
 * Interface for a DataContainer concept
 * A DataContainer is a fine tuned data structure which holds and exposes data.
 * The container is a passive Object, its not an classical Object (as it SHOULD NOT and DOES NOT provide behaviour).
 * A DataContainer SHOULD maintain and guarantee the integrity of its internal constraints.
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface DataContainerInterface
{
    /**
     * Define a property on data container
     * @param string $property
     * @param array $constraints
     * @param null $defaultValue
     */
    public function defineProperty(string $property, array $constraints = [], $defaultValue = null): void;

    /**
     * Populate a property with value
     * @param string $property
     * @param $value
     */
    public function addPropertyValue(string $property, $value): void;

    /**
     * Get a property value
     * @param string $property
     * @return mixed
     */
    public function getPropertyValue(string $property);

    /**
     * Check if a property is valid
     * @param string $property
     * @return bool
     */
    public function isValidProperty(string $property): bool;

    /**
     * Get the defined list of properties with the associated constraints
     * @return array
     */
    public function getProperties(): array;

    /**
     * Get the list of properties names
     * @return array
     */
    public function getPropertiesList(): array;

    /**
     * Return ONLY the current state of data
     * - will not return default property values or property values that have not been explicitly set.
     * - handles DataContainerInterface property values normalization
     * - return simple array with key->value OR multidimensional array with key->array but never object values
     * @return array
     */
    public function toArray(): array;

    /**
     * Normalize all properties of this container:
     * - handles default value resolution
     * - handles DataContainerInterface property values normalization
     * - return simple array with key->value OR multidimensional array with key->array but never object values
     * @return array
     */
    public function normalizeData(): array;

    /**
     * Add data from array
     * @param array $data
     * @param bool $skipExtraProperties
     * @param bool $skipMissingProperties
     */
    public function fromArray(array $data, bool $skipExtraProperties = true, bool $skipMissingProperties = true): void;

}
