<?php

namespace Craft\Security\Authorization\Registry;

use Craft\Data\Container\DataContainerInterface;
use Craft\Data\Container\DataContainerTrait;

/**
 * Class RbacPermission
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string name
 * @property array operations
 *
 */
class RbacPermission implements DataContainerInterface
{
    use DataContainerTrait;

    public function __construct(array $data = null)
    {
        $this->defineProperty('name', ['string']);
        $this->defineProperty('operations', ['array']);

        if ($data) {
            $this->fromArray($data, false, false);
        }
    }
}
