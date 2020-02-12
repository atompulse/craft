<?php

namespace Craft\Security\Authentication;

use Craft\Security\Authentication\Exception\TokenAuthenticatorException;
use Craft\Security\User\SecurityUserInterface;
use Craft\Security\User\SecurityUserProvider;
use Craft\Security\User\SecurityUserProviderInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class TokenAuthenticator
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class TokenAuthenticator extends AbstractGuardAuthenticator implements TokenAuthenticatorInterface
{

    protected const AUTH_TOKEN_MISSING = 1000;
    protected const AUTH_TOKEN_EXPIRED = 2000;

    /**
     * @var SecurityUserProvider
     */
    protected $securityUserProvider;

    /**
     * TokenAuthenticator constructor.
     * @param SecurityUserProviderInterface $securityUserProvider
     */
    public function __construct(SecurityUserProviderInterface $securityUserProvider)
    {
        $this->securityUserProvider = $securityUserProvider;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request.
     * Returning false will cause this authenticator to be skipped.
     */
    public function supports(Request $request)
    {
        return true;
    }

    /**
     * Called on every request and should read the token
     * from the request and return it.
     * These credentials are later passed as the first argument of getUser().
     *
     * @param Request $request
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->headers->get(TokenKey::HEADER_NAME) ?? $request->query->get(TokenKey::QUERY_NAME),
        ];
    }

    /**
     * The $credentials argument is the value returned by getCredentials().
     * Should return an object that implements UserInterface.
     * Returning null (or throw an AuthenticationException) authentication will fail.
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return SecurityUserInterface|UserInterface|null
     * @throws \Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->getSecurityUser($credentials['token'] ?? '');
    }

    /**
     * @param string $token
     * @return SecurityUserInterface
     * @throws \Exception
     */
    public function getSecurityUser(string $token): SecurityUserInterface
    {
        if (strlen($token) === 0) {
            $exception = new TokenAuthenticatorException('Authentication token required');
            $exception->addError(new TokenAuthenticatorError('Authentication token required', TokenKey::HEADER_NAME));

            throw $exception;
        }

        if ($this->securityUserProvider->isTokenExpired($token)) {
            $exception = new TokenAuthenticatorException('Authentication token expired');
            $exception->addError(new TokenAuthenticatorError('Authentication token expired', TokenKey::HEADER_NAME));

            throw $exception;
        }

        return $this->securityUserProvider->getUserFromToken($token);
    }

    /**
     * If getUser() returns a User object, this method is called.
     * Verify if the credentials are correct.
     * To pass authentication, return true,
     * anything else (or throw an AuthenticationException) and the authentication will fail.
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw $exception;
    }

    /**
     * Called if the client accesses a URI/resource that requires authentication,
     * but no authentication details were sent.
     * Should return a Response object that helps the user authenticate (e.g. a 401 response that says "token is missing!").
     * @param Request $request
     * @param AuthenticationException|null $authException
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $exception = new TokenAuthenticatorException('Authentication Required');
        $exception->addError(new TokenAuthenticatorError('Authentication Required', TokenKey::HEADER_NAME));

        throw $exception;
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }

}
