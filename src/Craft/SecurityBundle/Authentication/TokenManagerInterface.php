<?php

namespace Craft\SecurityBundle\Authentication;

use ParagonIE\Paseto\JsonToken;

/**
 * Interface TokenManagerInterface
 * @package Craft\SecurityBundleBundle\Authentication
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface TokenManagerInterface
{

    public function generateToken(array $data): string;

    public function generateTemporaryToken(array $data, string $lifetime): string;

    public function decodeToken(string $providedToken): JsonToken;

}
