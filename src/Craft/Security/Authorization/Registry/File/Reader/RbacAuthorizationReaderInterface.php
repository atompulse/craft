<?php

namespace Craft\Security\Authorization\Registry\File\Reader;

/**
 * Interface RbacAuthorizationReaderInterface
 * @package Craft\Security\Authorization\Registry\File\Reader
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
interface RbacAuthorizationReaderInterface
{
    /**
     * @param string $rolesFile
     * @return array
     */
    public static function readRoles(string $rolesFile): array;

    /**
     * @param string $permissionsFile
     * @return array
     */
    public static function readPermissions(string $permissionsFile): array;

}
