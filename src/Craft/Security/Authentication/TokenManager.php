<?php

namespace Craft\Security\Authentication;

use Craft\Security\Authentication\Exceptions\TokenValidationException;

use Craft\Security\User\UserDataInterface;
use Exception;
use ParagonIE\Paseto\Builder;
use ParagonIE\Paseto\Exception\RuleViolation;
use ParagonIE\Paseto\JsonToken;
use ParagonIE\Paseto\Protocol\Version2;
use ParagonIE\Paseto\Purpose;
use ParagonIE\Paseto\Keys\SymmetricKey;
use ParagonIE\Paseto\Exception\PasetoException;
use ParagonIE\Paseto\Parser;
use ParagonIE\Paseto\Rules\IssuedBy;
use ParagonIE\Paseto\ProtocolCollection;
use Psr\Log\LoggerInterface;

/**
 * Class TokenManager
 * @package Craft\Security\Authentication
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class TokenManager implements TokenManagerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $secretKey;

    public function __construct(string $secretKey, LoggerInterface $logger)
    {
        $this->secretKey = $secretKey;
        $this->logger = $logger;
    }

    /**
     * @param UserDataInterface $data
     * @param string $lifetime
     * @return string
     * @throws PasetoException
     * @throws \ParagonIE\Paseto\Exception\InvalidKeyException
     * @throws \ParagonIE\Paseto\Exception\InvalidPurposeException
     */
    public function generateTemporaryToken(UserDataInterface $data, string $lifetime): string
    {
        $sharedKey = new SymmetricKey($this->secretKey, new Version2());

        $claims = clone $data;

        $claims->expires = true;
        $claims->expireDate = $lifetime;

        $token = (new Builder())
            ->setKey($sharedKey)
            ->setVersion(new Version2())
            ->setPurpose(Purpose::local())
            ->setIssuer('CRAFT')
            // store data
            ->setClaims($claims->toArray());

        return $token->toString();
    }

    /**
     * @param UserDataInterface $data
     * @return string
     * @throws PasetoException
     * @throws \ParagonIE\Paseto\Exception\InvalidKeyException
     * @throws \ParagonIE\Paseto\Exception\InvalidPurposeException
     */
    public function generateToken(UserDataInterface $data): string
    {
        $sharedKey = new SymmetricKey($this->secretKey, new Version2());

        $claims = clone $data;
        $claims->expires = false;

        $token = (new Builder())
            ->setKey($sharedKey)
            ->setVersion(new Version2())
            ->setPurpose(Purpose::local())
            ->setIssuer('CRAFT')
            // store data
            ->setClaims($claims->toArray());

        return $token->toString();
    }

    /**
     * @param string $providedToken
     * @return JsonToken
     * @throws PasetoException
     * @throws \ParagonIE\Paseto\Exception\InvalidPurposeException
     * @throws \ParagonIE\Paseto\Exception\InvalidVersionException
     */
    public function decodeToken(string $providedToken): JsonToken
    {
        $sharedKey = new SymmetricKey($this->secretKey, new Version2());

        /*
        NotExpired rule - why not use this rule:
        if this rule is added then its impossible to check if the token has expiration or not
        because there are no means to determine if expiration rule should be added or not
        */

        // adding rules to be checked against the token
        $parser = (new Parser())
            ->setKey($sharedKey)
            ->addRule(new IssuedBy('CRAFT'))
            ->setPurpose(Purpose::local())
            // only allow version 2
            ->setAllowedVersions(ProtocolCollection::v2());

        try {
            $token = $parser->parse($providedToken);
        } catch (Exception $err) {
            if ($err instanceof RuleViolation) {
                $this->logger->info('Token invalid', ['error' => $err]);
            } else {
                $this->logger->critical('Failed to decode token', ['error' => $err]);
            }

            throw new TokenValidationException("Token not valid");
        }

        return $token;
    }
}
