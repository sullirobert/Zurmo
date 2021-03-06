<?php
    /*********************************************************************************
     * Zurmo is a customer relationship management program developed by
     * Zurmo, Inc. Copyright (C) 2013 Zurmo Inc.
     *
     * Zurmo is free software; you can redistribute it and/or modify it under
     * the terms of the GNU Affero General Public License version 3 as published by the
     * Free Software Foundation with the addition of the following permission added
     * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
     * IN WHICH THE COPYRIGHT IS OWNED BY ZURMO, ZURMO DISCLAIMS THE WARRANTY
     * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
     *
     * Zurmo is distributed in the hope that it will be useful, but WITHOUT
     * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
     * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
     * details.
     *
     * You should have received a copy of the GNU Affero General Public License along with
     * this program; if not, see http://www.gnu.org/licenses or write to the Free
     * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
     * 02110-1301 USA.
     *
     * You can contact Zurmo, Inc. with a mailing address at 27 North Wacker Drive
     * Suite 370 Chicago, IL 60606. or at email address contact@zurmo.com.
     *
     * The interactive user interfaces in original and modified versions
     * of this program must display Appropriate Legal Notices, as required under
     * Section 5 of the GNU Affero General Public License version 3.
     *
     * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
     * these Appropriate Legal Notices must retain the display of the Zurmo
     * logo and Zurmo copyright notice. If the display of the logo is not reasonably
     * feasible for technical reasons, the Appropriate Legal Notices must display the words
     * "Copyright Zurmo Inc. 2013. All rights reserved".
     ********************************************************************************/

    /**
     * Helper class for Permissions.
     */
    class PermissionsUtil
    {
        /**
         * @return array of all module permissions data
         * Example of a return just for the accounts module.
         * Normally all the applicable modules permissions
         * would be returned in the array.
         * @code
            <?php
                $compareData = array(
                    'AccountsModule' => array(
                        'CREATE' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                        'CHANGE_OWNER' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                        'CHANGE_PERMISSIONS' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                        'DELETE' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                        'READ' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                        'WRITE' => array(
                            'explicit'    => null,
                            'inherited'   => null,
                        ),
                    ),
                );
            ?>
         * @endcode
         */
        public static function getAllModulePermissionsDataByPermitable(Permitable $permitable)
        {
            $data        = array();
            $modules     = Module::getModuleObjects();
            $permissions = PermissionsUtil::getPermissions();
            foreach ($modules as $module)
            {
                if ($module instanceof SecurableModule)
                {
                    $moduleClassName = get_class($module);
                    $moduleName      = $module->getName();
                    $item            = NamedSecurableItem::getByName($moduleClassName);
                    if (!empty($permissions))
                    {
                        foreach ($permissions as $permission)
                        {
                            $explicit  = PermissionsUtil::resolveExplicitOrInheritedPermission(
                                            $item->getExplicitActualPermissions ($permitable),
                                            $permission);
                            $inherited = PermissionsUtil::resolveExplicitOrInheritedPermission(
                                            $item->getInheritedActualPermissions($permitable),
                                            $permission);
                            $actual = PermissionsUtil::resolveActualPermission(
                                            $item->getActualPermissions         ($permitable),
                                            $permission);
                            $data[$moduleClassName][$permission] = array(
                                'explicit'  => PermissionsUtil::resolvePermissionForData($explicit),
                                'inherited' => PermissionsUtil::resolvePermissionForData($inherited),
                                'actual'    => PermissionsUtil::resolvePermissionForData($actual),
                            );
                        }
                    }
                }
            }
            return $data;
        }

        /**
         * Public for testing purposes
         */
        public static function resolveExplicitOrInheritedPermission(
            $permissions, $matchingPermission)
        {
            assert('in_array($matchingPermission, PermissionsUtil::getPermissions())');
            list($allowPermissions, $denyPermissions) = $permissions;
            if ($matchingPermission == ($denyPermissions & $matchingPermission))
            {
                return Permission::DENY;
            }
            if ($matchingPermission == ($allowPermissions & $matchingPermission))
            {
                return Permission::ALLOW;
            }
            return Permission::NONE;
        }

        protected static function resolveActualPermission(
            $permissions, $matchingPermission)
        {
            list($allowPermissions, $denyPermissions) = $permissions;
            if ($matchingPermission == ($denyPermissions & $matchingPermission))
            {
                return Permission::DENY;
            }
            if ($matchingPermission == ($allowPermissions & $matchingPermission))
            {
                return Permission::ALLOW;
            }
            return Permission::NONE;
        }

        protected static function resolvePermissionForData($permission)
        {
            if     ($permission == Permission::DENY ||
                    $permission == Permission::ALLOW)
            {
                return $permission;
            }
            elseif ($permission == Permission::NONE)
            {
                return null;
            }
            throw new NotSupportedException();
        }

        protected static function getPermissions()
        {
            return array(
                Permission::READ,
                Permission::WRITE,
                Permission::DELETE,
                Permission::CHANGE_PERMISSIONS,
                Permission::CHANGE_OWNER,
            );
        }

        /**
         * Given a moduleClassName, what is the actual read permission?
         * Permission::DENY, Permission::ALLOW, or Permission::NONE?
         */
        public static function getActualPermissionDataForReadByModuleNameForCurrentUser($moduleClassName)
        {
            assert('is_string($moduleClassName)');
            $item  = NamedSecurableItem::getByName($moduleClassName);
            return PermissionsUtil::resolveActualPermission($item->getActualPermissions(), Permission::READ);
        }
    }
?>
