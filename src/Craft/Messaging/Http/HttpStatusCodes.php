<?php

namespace Craft\Messaging\Http;

use ReflectionClass;
use RuntimeException;

/**
 * Class HttpStatusCodes
 * @package Craft\Messaging\Http
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class HttpStatusCodes
{
    // STANDARD statuses

    // everything is okay, indicates that no errors occurred
    const OK = 200;

    // everything is okay, but there’s no data to return
    // indicates the the request was processed correctly but there’s nothing to return
    const NO_RESULTS = 204;

    // the requested information is incomplete or malformed
    // indicates the input is not valid
    const INVALID_INPUT_ERROR = 400;

    // requested resource doesn’t exist
    // indicates that the requested URL is not valid
    const INVALID_RESOURCE_ERROR = 404;

    // the server throws an error, completely unexpected
    // indicates that the request could not be processed due to a server error
    const UNEXPECTED_ERROR = 500;


    // AUTH statuses

    // an access token isn’t provided, or is invalid
    const AUTHENTICATION_ERROR = 401;

    // an access token is valid, but requires more privileges
    const AUTHORIZATION_ERROR = 403;

    /**
     * @param string $status
     * @return int
     */
    public static function getCode(string $status): int
    {
        $constants = (new ReflectionClass(__CLASS__))->getConstants();

        foreach ($constants as $name => $value) {
            if ($status === $name) {
                return $value;
            }
        }

        throw new RuntimeException("[$status] is not defined");
    }

    /**
     * @param int $code
     * @return string
     */
    public static function getName(int $code): string
    {
        $constants = (new ReflectionClass(__CLASS__))->getConstants();

        foreach ($constants as $name => $value) {
            if ($code === $value) {
                return $name;
            }
        }

        throw new RuntimeException("[$code] is not defined");
    }

}
