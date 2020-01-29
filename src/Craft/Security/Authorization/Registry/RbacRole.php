<?php

namespace Craft\Security\Authorization\Registry;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class RbacRole
 * @package Craft\Security\Authorization\Registry
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string name
 * @property array permissions
 */
class RbacRole implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('name', ['string']);
        $this->defineProperty('permissions', ['array']);

        if ($data) {
            $this->fromArray($data, false, false);
        }
    }
}
