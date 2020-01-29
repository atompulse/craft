<?php

namespace Craft\Data\Container;

/**
 * Interface DataContainerInterface
 * @package Craft\Data\Container
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
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
     * Get data as array
     * @return array
     */
    public function toArray(): array;

    /**
     * Add data from array
     * @param array $data
     * @param bool $skipExtraProperties
     * @param bool $skipMissingProperties
     */
    public function fromArray(array $data, bool $skipExtraProperties = true, bool $skipMissingProperties = true): void;

    /**
     * Normalize all properties of this container:
     * - handles default value resolution
     * - handles DataContainerInterface property values normalization
     * - return simple array with key->value OR multidimensional array with key->array but never object values
     * @param string|null $property
     * @return mixed
     */
    public function normalizeData(string $property = null): array;
}
