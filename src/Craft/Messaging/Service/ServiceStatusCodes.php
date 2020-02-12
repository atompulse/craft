<?php

namespace Craft\Messaging\Service;

/**
 * Class ServiceStatusCodes
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class ServiceStatusCodes
{
    //\\ STANDARD statuses

    // everything is okay
    // indicates that no errors occurred
    const OK = 'OK';

    // everything is okay, but there’s no data to return
    // indicates the the request was processed correctly but there’s no content to return
    const NO_RESULTS = 'NO_RESULTS';

    // the requested information is incomplete or malformed
    // indicates the input is not valid
    const INVALID_INPUT = 'INVALID_INPUT';

    // completely unexpected error
    // indicates that the request could not be processed due to a internal error
    const UNEXPECTED_ERROR = 'UNEXPECTED_ERROR';


    //\\ AUTH statuses

    // an access token isn’t provided or is invalid
    const AUTHENTICATION_ERROR = 'AUTHENTICATION_ERROR';

    // an access token is valid, but requires more privileges
    const AUTHORIZATION_ERROR = 'AUTHORIZATION_ERROR';
}
