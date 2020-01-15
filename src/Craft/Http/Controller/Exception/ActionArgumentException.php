<?php

namespace Craft\Http\Controller\Exception;

use Craft\Exception\ContextualExceptionTrait;

/**
 * Class ActionArgumentException
 * @package Craft\Http\Controller\Exception
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ActionArgumentException extends \Exception implements ActionArgumentExceptionInterface
{
    use ContextualExceptionTrait;
}
