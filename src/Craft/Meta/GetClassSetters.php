<?php

namespace Craft\Meta;

use Craft\Data\Processor\ArrayProcessor;
use ReflectionMethod;

/**
 * Class GetClassSetters
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class GetClassSetters
{
    public function __invoke(string $class)
    {
        return ArrayProcessor::filterArrayValuesStartingWith(get_class_methods($class), 'set');
    }
}
