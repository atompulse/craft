<?php

namespace Craft\Security\Authorization;

/**
 * Interface RoutesAuthorizationRegistryInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface RoutesAuthorizationRegistryInterface
{
    /**
     * @param string $route
     * @return array
     */
    public function getRequirements(string $route): array;
}
