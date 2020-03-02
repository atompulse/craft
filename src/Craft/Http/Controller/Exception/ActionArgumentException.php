<?php

namespace Craft\Http\Controller\Exception;

use Craft\Exception\ContextualExceptionTrait;
use Exception;

/**
 * Class ActionArgumentException
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ActionArgumentException extends Exception implements ActionArgumentExceptionInterface
{
    use ContextualExceptionTrait;
}
