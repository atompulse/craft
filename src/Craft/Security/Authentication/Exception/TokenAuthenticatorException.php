<?php

namespace Craft\Security\Authentication\Exception;

use Craft\Exception\ContextualExceptionInterface;
use Craft\Exception\ContextualExceptionTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class TokenAuthenticatorException
 * @package Craft\Security\Authentication\Exceptions
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class TokenAuthenticatorException extends AuthenticationException implements ContextualExceptionInterface
{
    use ContextualExceptionTrait;
}
