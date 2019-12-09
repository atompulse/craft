<?php

namespace Craft\SecurityBundle\Authorization;

/**
 * Interface RoutesAuthorizationRegistryInterface
 * @package Craft\SecurityBundleBundle\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface RoutesAuthorizationRegistryInterface
{
    /**
     * @param string $route
     * @return array
     */
    public function getRequirements(string $route): array;
}
