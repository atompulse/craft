<?php

namespace Craft\Exception;

use Atompulse\Component\Domain\Data\DataContainerInterface;

/**
 * Interface ContextualErrorInterface
 * @package Craft\Exception
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface ContextualErrorInterface extends DataContainerInterface
{
    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @return string
     */
    public function getContext(): string;
}
