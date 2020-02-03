<?php

namespace Craft\Messaging\Service;

use Craft\Data\Container\DataContainerTrait;
use Craft\Messaging\ResponseInterface;

/**
 * Class ServiceResponse
 * @package Craft\Messaging\Service
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string status
 * @property array errors
 */
class ServiceResponse implements ResponseInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('status', ['string']);
        $this->defineProperty('errors', ['array', 'null']);

        if ($data !== null) {
            $this->fromArray($data, true, false);
        }
    }

    public function getStatus(): string
    {
        return $this->properties['status'] ?? ServiceStatusCodes::OK;
    }

    public function getErrors(): ?array
    {
        return $this->properties['errors'] ?? null;
    }

    public function addError(ServiceError $error)
    {
        $this->addPropertyValue('errors', $error);
    }

}
