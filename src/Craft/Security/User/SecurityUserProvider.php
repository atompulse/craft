<?php

namespace Craft\Security\User;

use Craft\Security\Authentication\TokenManager;
use Craft\Security\Authentication\TokenManagerInterface;
use Craft\Security\Authorization\AuthorizationRegistryInterface;
use DateTime;
use Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserProvider
 * @package Craft\Security\User
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class SecurityUserProvider implements SecurityUserProviderInterface
{
    /**
     * @var AuthorizationRegistryInterface
     */
    protected $authorizationRegistry;
    /**
     * @var TokenManager
     */
    protected $tokenManager;
    /**
     * @var UserRegistryInterface
     */
    protected $userRegistry;

    public function __construct(
        TokenManagerInterface $tokenManager,
        AuthorizationRegistryInterface $authorizationRegistry,
        UserRegistryInterface $userRegistry
    ) {
        $this->authorizationRegistry = $authorizationRegistry;
        $this->tokenManager = $tokenManager;
        $this->userRegistry = $userRegistry;
    }

    /**
     * @param string $token
     * @return SecurityUserInterface
     */
    public function getUserFromToken(string $token): SecurityUserInterface
    {
        $data = $this->getTokenData($token);

        $operations = $this->authorizationRegistry->getRoleOperationsList($data['role']);

        return $this->userRegistry->getUser($data, $operations);
    }

    /**
     * @param string $token
     * @return array
     */
    protected function getTokenData(string $token): array
    {
        return $this->tokenManager->decodeToken($token)->getClaims();
    }

    /**
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function isTokenExpired(string $token): bool
    {
        $data = $this->getTokenData($token);

        if ($data['expires']) {
            $expirationDate = new DateTime($data['expires']);

            return $expirationDate < (new DateTime());
        }

        return false;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        throw new UsernameNotFoundException("Method [loadUserByUsername] not supported");
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException  if the user is not supported
     * @throws UsernameNotFoundException if the user is not found
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return $class === SecurityUser::class;
    }
}
