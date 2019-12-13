<?php

namespace Craft\Security\User;

/**
 * Interface UserRegistryInterface
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface UserRegistryInterface
{
    /**
     * @param array $data
     * @param array $operations
     * @return SecurityUserInterface
     */
    public function getUser(array $data, array $operations): SecurityUserInterface;
}
