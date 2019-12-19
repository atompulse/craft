<?php

namespace Craft\Security\Authentication;

use Craft\Security\User\UserDataInterface;
use ParagonIE\Paseto\JsonToken;

/**
 * Interface TokenManagerInterface
 * @package Craft\Security\Authentication
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface TokenManagerInterface
{

    /**
     * @param UserDataInterface $data
     * @return string
     */
    public function generateToken(UserDataInterface $data): string;

    /**
     * @param UserDataInterface $data
     * @param string $lifetime
     * @return string
     */
    public function generateTemporaryToken(UserDataInterface $data, string $lifetime): string;

    /**
     * @param string $providedToken
     * @return JsonToken
     */
    public function decodeToken(string $providedToken): JsonToken;

}
