<?php

namespace Craft\Data\Container\Exception;

/**
 * Class PropertyMissingException
 *
 * Thrown when a container is populated from an array using the DataContainerInterface::fromArray,
 * but the property is missing from the input array and $skipMissingProperties is false
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class PropertyMissingException extends \Exception
{
}
