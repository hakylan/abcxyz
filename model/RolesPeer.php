<?php
/**
 * Roles
 * @version		$Id$
 * @package		Model
 */

require_once dirname(__FILE__) .'/Base/RolesBase.php';
class RolesPeer extends \RolesBase {

    public static function switchPermissionByRole($role_id,$codePermission, $value){
        $role= \Roles::findOneById($role_id);
        $arrRole = $role->getRoleByRoleS();
        $numSwitch = 0;
        foreach($arrRole as $oneRole) {
            $roleOne = \Roles::findOneByLabel($oneRole);
            $switchOne = \Permissions::findOneByCodeAndRoleId($codePermission, $roleOne->getId());
            if(isset($switchOne)){
                $switchOne->setOn($value);
                if($switchOne->save()){
                    $numSwitch++;
                }
            }
        }
        return $numSwitch;
    }

}