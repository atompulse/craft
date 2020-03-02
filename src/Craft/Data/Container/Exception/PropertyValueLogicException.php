<?php

namespace Craft\Data\Container\Exception;

use Craft\Exception\ContextualExceptionInterface;
use Craft\Exception\ContextualExceptionTrait;
use LogicException;

/**
 * Class PropertyValueLogicException
 *
 * Thrown when a value is not acceptable from a logic point of view
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class PropertyValueLogicException extends LogicException implements ContextualExceptionInterface
{
    use ContextualExceptionTrait;
}
