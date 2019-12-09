<?php

namespace Craft\SecurityBundle\Authorization;

/**
 * Interface AuthorizationRegistryInterface
 * @package Craft\SecurityBundleBundle\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface AuthorizationRegistryInterface
{
    public function getRoleOperationsList(string $role): array;
}
