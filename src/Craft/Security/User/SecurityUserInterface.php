<?php

namespace Craft\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface SecurityUserInterface
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface SecurityUserInterface extends UserInterface
{
    /**
     * @return UserDataInterface
     */
    public function getUserData(): UserDataInterface;

    /**
     * @return string
     */
    public function getUserRole(): string;
}
