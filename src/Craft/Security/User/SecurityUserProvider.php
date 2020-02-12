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
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
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
    /**
     * @var string
     */
    protected $userDataClass;

    public function __construct(
        TokenManagerInterface $tokenManager,
        AuthorizationRegistryInterface $authorizationRegistry,
        UserRegistryInterface $userRegistry,
        $userDataClass
    ) {
        $this->authorizationRegistry = $authorizationRegistry;
        $this->tokenManager = $tokenManager;
        $this->userRegistry = $userRegistry;
        $this->userDataClass = $userDataClass;
    }

    /**
     * @param string $token
     * @return SecurityUserInterface
     */
    public function getUserFromToken(string $token): SecurityUserInterface
    {
        /** @var UserDataInterface $userData */
        $userData = $this->getTokenData($token);

        $operations = $this->authorizationRegistry->getRoleOperationsList($userData->getRole());

        return $this->userRegistry->getUser($userData, $operations);
    }

    /**
     * @param string $token
     * @return UserDataInterface
     */
    protected function getTokenData(string $token): UserDataInterface
    {
        $rawData = $this->tokenManager->decodeToken($token)->getClaims();
        $factory = new \ReflectionClass($this->userDataClass);
        $userData = $factory->newInstance($rawData);

        return $userData;
    }

    /**
     * @param string $token
     * @return bool
     * @throws Exception
     */
    public function isTokenExpired(string $token): bool
    {
        $data = $this->getTokenData($token);

        if ($data->getExpires()) {
            $expirationDate = new DateTime($data->getExpireDate());

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
