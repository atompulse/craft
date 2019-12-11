<?php

namespace Craft\Security\Authentication;

use Craft\Security\User\SecurityUserInterface;

/**
 * Interface TokenAuthenticatorInterface
 * @package Craft\Security\Authentication
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface TokenAuthenticatorInterface
{
    /**
     * @param string $token
     * @return SecurityUserInterface
     */
    public function getSecurityUser(string $token): SecurityUserInterface;
}
