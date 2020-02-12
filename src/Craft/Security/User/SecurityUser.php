<?php

namespace Craft\Security\User;

/**
 * Class User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class SecurityUser implements SecurityUserInterface
{
    /**
     * @var UserDataInterface
     */
    protected $userData = [];

    /**
     * @var array
     */
    protected $operations = [];

    public function __construct(UserDataInterface $userData, array $operations)
    {
        $this->userData = $userData;
        $this->operations = array_merge($this->operations, $operations);
    }

    /**
     * @return UserDataInterface
     */
    public function getUserData(): UserDataInterface
    {
        return $this->userData;
    }

    /**
     * @return string
     */
    public function getUserRole(): string
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
