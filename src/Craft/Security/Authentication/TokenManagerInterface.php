<?php

namespace Craft\Security\Authentication;

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
     * @param array $data
     * @return string
     */
    public function generateToken(array $data): string;

    /**
     * @param array $data
     * @param string $lifetime
     * @return string
     */
    public function generateTemporaryToken(array $data, string $lifetime): string;

    /**
     * @param string $providedToken
     * @return JsonToken
     */
    public function decodeToken(string $providedToken): JsonToken;

}
