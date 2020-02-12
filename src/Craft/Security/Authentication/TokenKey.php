<?php

namespace Craft\Security\Authentication;

/**
 * Class TokenKey
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class TokenKey
{
    // name of the header parameter
    const HEADER_NAME = 'X-TOKEN';
    // name of the query parameter
    const QUERY_NAME = 'x';
}
