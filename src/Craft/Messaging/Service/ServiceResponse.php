<?php

namespace Craft\Messaging\Service;

use Craft\Data\Container\DataContainerTrait;
use Craft\Exception\ContextualErrorInterface;
use Craft\Messaging\ResponseInterface;

/**
 * Class ServiceResponse
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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->getPropertyValue('status') ?? ServiceStatusCodes::OK;
    }

    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->getPropertyValue('errors') ?? null;
    }

    /**
     * @param ContextualErrorInterface $error
     */
    public function addError(ContextualErrorInterface $error): void
    {
        $this->addPropertyValue('errors', $error);
    }

}
