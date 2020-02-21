<?php

namespace Craft\Meta;

use Craft\Data\Processor\ArrayProcessor;

/**
 * Class GetClassGetters
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class GetClassGetters
{
    public function __invoke(string $class)
    {
        return ArrayProcessor::filterArrayValuesStartingWith(get_class_methods($class), 'get');
    }
}
