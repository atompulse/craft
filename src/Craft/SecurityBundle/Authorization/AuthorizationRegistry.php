<?php

namespace Craft\SecurityBundle\Authorization;

use Craft\SecurityBundle\Authorization\Exceptions\RbacPermissionWithNoOperationsException;
use Craft\SecurityBundle\Authorization\Exceptions\RbacRoleNotDefinedException;
use Craft\SecurityBundle\Authorization\Reader\RbacAuthorizationReaderInterface;
use Exception;

/**
 * Class AuthorizationRegistry
 * @package Craft\SecurityBundleBundle\Authorization
 *
 * @author Petru Cojocar <petru.cojocar@gmail.com>
 */
class AuthorizationRegistry implements AuthorizationRegistryInterface
{
    /**
     * @var RbacAuthorizationReaderInterface
     */
    protected $rbacAuthorizationReader;
    /**
     * @var string
     */
    protected $rbacRolesFile;
    /**
     * @var string
     */
    protected $rbacPermissionsFile;

    protected $index;

    public function __construct(
        string $rbacRolesFile,
        string $rbacPermissionsFile,
        RbacAuthorizationReaderInterface $rbacAuthorizationReader
    ) {
        $this->rbacAuthorizationReader = $rbacAuthorizationReader;
        $this->rbacRolesFile = $rbacRolesFile;
        $this->rbacPermissionsFile = $rbacPermissionsFile;

        $this->initRegistry();
    }

    protected function initRegistry()
    {
        $roles = $this->rbacAuthorizationReader::readRoles($this->rbacRolesFile);
        $permissions = $this->rbacAuthorizationReader::readPermissions($this->rbacPermissionsFile);

        $permissionsIndex = [];
        $rolesIndex = [];

        foreach ($permissions as $permission) {
            $permissionsIndex[$permission->name] = $permission->operations;
        }

        $roleOpsExtractor = function (array $permissions) use ($permissionsIndex) {
            $ops = [];
            foreach ($permissions as $permission) {
                if (!isset($permissionsIndex[$permission])) {
                    throw new RbacPermissionWithNoOperationsException('AuthorizationRegistry permission [' . $permission . '] has no operations defined');
                }
                $ops = array_merge($ops, $permissionsIndex[$permission]);
            }
            return array_unique($ops);
        };

        foreach ($roles as $role) {
            $rolesIndex[$role->name] = $roleOpsExtractor($role->permissions);
        }

        $this->index = $rolesIndex;
    }

    /**
     * @param string $role
     * @return array
     * @throws Exception
     */
    public function getRoleOperationsList(string $role): array
    {
        if (!isset($this->index[$role])) {
            throw new RbacRoleNotDefinedException('AuthorizationRegistry role [' . $role . '] is not defined');
        }

        return $this->index[$role];
    }
}
