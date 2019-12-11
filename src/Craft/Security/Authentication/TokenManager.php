<?php

namespace Craft\Security\Authentication;

use Craft\Security\Authentication\Exceptions\TokenValidationException;

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
use ParagonIE\Paseto\Rules\NotExpired;
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
     * @param array $data
     * @param string $lifetime
     * @return string
     */
    public function generateTemporaryToken(array $data, string $lifetime): string
    {
        $sharedKey = new SymmetricKey($this->secretKey, new Version2());

        $data['expires'] = $lifetime;

        $token = (new Builder())
            ->setKey($sharedKey)
            ->setVersion(new Version2())
            ->setPurpose(Purpose::local())
            ->setIssuer('CRAFT')
            // store data
            ->setClaims($data);

        return $token->toString();
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateToken(array $data): string
    {
        $sharedKey = new SymmetricKey($this->secretKey, new Version2());

        $data['expires'] = false;

        $token = (new Builder())
            ->setKey($sharedKey)
            ->setVersion(new Version2())
            ->setPurpose(Purpose::local())
            ->setIssuer('CRAFT')
            // store data
            ->setClaims($data);

        return $token->toString();
    }


    /**
     * @param string $providedToken
     * @return JsonToken
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
