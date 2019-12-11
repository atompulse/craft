<?php

namespace Craft\Security\Authorization\Registry;

use Atompulse\Component\Domain\Data\DataContainer;
use Atompulse\Component\Domain\Data\DataContainerInterface;

/**
 * Class RbacPermission
 * @package Craft\Security\Authorization\Registry
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 * @property string name
 * @property array operations
 *
 */
class RbacPermission implements DataContainerInterface
{
    use DataContainer;

    public function __construct(array $data = null)
    {
        $this->defineProperty('name', ['string']);
        $this->defineProperty('operations', ['array']);

        if ($data) {
            $this->fromArray($data, false, false);
        }
    }
}
