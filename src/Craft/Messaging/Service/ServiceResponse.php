<?php

namespace Craft\Messaging\Service;

use Atompulse\Component\Domain\Data\DataContainer;
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
    use DataContainer;

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
        return $this->properties['status'] ?? null;
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
