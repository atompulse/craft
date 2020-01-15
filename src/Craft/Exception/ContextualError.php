<?php

namespace Craft\Exception;

use Atompulse\Component\Domain\Data\DataContainer;

/**
 * Class ContextualError
 * @package Craft\Exception
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string message
 * @property string context
 *
 */
class ContextualError implements ContextualErrorInterface
{
    use DataContainer;

    /**
     * ContextualError constructor.
     * @param string $message
     * @param string $context
     */
    public function __construct(string $message, string $context = '')
    {
        $this->defineProperty('message', ['string']);
        $this->defineProperty('context', ['string', 'null']);

        $this->addPropertyValue('message', $message);
        $this->addPropertyValue('context', $context);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->properties['message'];
    }

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->properties['context'];
    }
}
