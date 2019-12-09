<?php

namespace Craft\SecurityBundle\User;

/**
 * Class User
 * @package Craft\SecurityBundleBundle\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class SecurityUser implements SecurityUserInterface
{
    /**
     * @var array
     */
    protected $userData = [];

    /**
     * @var array
     */
    protected $operations = ['ROLE_ACCESS_API'];

    public function __construct(array $userData, array $operations)
    {
        $this->userData = $userData;
        $this->operations = array_merge($this->operations, $operations);
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return $this->userData;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->userData['role'] ?? null;
    }

    /**
     * Get Operations allowed for this user
     * @return array
     */
    public function getRoles(): array
    {
        return $this->operations;
    }

    public function getPassword()
    {
        return '';
    }

    public function getSalt()
    {
        return;
    }

    public function getUsername()
    {
        return '';
    }

    public function eraseCredentials()
    {
        return;
    }
}
