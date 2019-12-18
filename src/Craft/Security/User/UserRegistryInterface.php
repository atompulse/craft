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
     * @param UserDataInterface $userData
     * @param array $operations
     * @return SecurityUserInterface
     */
    public function getUser(UserDataInterface $userData, array $operations): SecurityUserInterface;

    public function getUserData(string $email, string $password): UserDataInterface;
}
