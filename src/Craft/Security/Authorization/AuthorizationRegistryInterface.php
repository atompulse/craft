<?php

namespace Craft\Security\Authorization;

/**
 * Interface AuthorizationRegistryInterface
 * @package Craft\Security\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface AuthorizationRegistryInterface
{
    public function getRoleOperationsList(string $role): array;
}
