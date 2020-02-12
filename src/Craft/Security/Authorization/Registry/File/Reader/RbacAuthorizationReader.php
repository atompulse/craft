<?php

namespace Craft\Security\Authorization\Registry\File\Reader;

use Craft\Security\Authorization\Registry\File\Reader\Exceptions\RbacDefinitionFileException;
use Craft\Security\Authorization\Registry\RbacPermission;
use Craft\Security\Authorization\Registry\RbacRole;
use Exception;
use Symfony\Component\Yaml\Yaml;

/**
 * Class RbacAuthorizationReader
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 *
 */
class RbacAuthorizationReader implements RbacAuthorizationReaderInterface
{
    /**
     * @param string $rolesFile
     * @return array
     */
    public static function readRoles(string $rolesFile): array
    {
        $rawData = self::retrieveRbacDefinitions($rolesFile);
        $roles = [];

        foreach ($rawData as $data) {
            $roles[] = new RbacRole($data);
        }

        return $roles;
    }

    /**
     * @param string $file
     * @return array
     * @throws Exception
     */
    protected static function retrieveRbacDefinitions(string $file): array
    {
        if (file_exists($file) && is_readable($file)) {
            return Yaml::parseFile($file);
        }

        throw new RbacDefinitionFileException("RbacAuthorizationReader::retrieveRbacDefinitions RBAC definition file [$file] is not readable or does not exists");
    }

    /**
     * @param string $permissionsFile
     * @return array
     */
    public static function readPermissions(string $permissionsFile): array
    {
        $rawData = self::retrieveRbacDefinitions($permissionsFile);
        $permissions = [];

        foreach ($rawData as $data) {
            $permissions[] = new RbacPermission($data);
        }

        return $permissions;
    }
}
