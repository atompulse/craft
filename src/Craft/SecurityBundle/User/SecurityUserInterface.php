<?php

namespace Craft\SecurityBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface SecurityUserInterface
 * @package Craft\SecurityBundleBundle\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface SecurityUserInterface extends UserInterface
{
    /**
     * @return array
     */
    public function getUserData(): array;

    /**
     * @return string
     */
    public function getUserRole(): string;
}
