<?php

namespace Craft\Http\Controller\Exception;

use Craft\Messaging\Service\ServiceError;

/**
 * Class ActionArgumentException
 * @package Craft\Http\Controller\Exception
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class ActionArgumentException extends \Exception implements ActionArgumentExceptionInterface
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @return array
     */
    public function getArgumentsErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[] = $error->normalizeData();
        }

        return $errors;
    }

    /**
     * @param ServiceError $error
     * @return mixed|void
     */
    public function addError(ServiceError $error)
    {
        $this->errors[] = $error;
    }
}