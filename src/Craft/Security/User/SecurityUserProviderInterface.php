<?php

namespace Craft\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Interface SecurityUserProviderInterface
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
interface SecurityUserProviderInterface extends UserProviderInterface
{
    /**
     * @param string $token
     * @return SecurityUserInterface
     */
    public function getUserFromToken(string $token): SecurityUserInterface;

    /**
     * @param string $token
     * @return bool
     */
    public function isTokenExpired(string $token): bool;
}
