<?php 
/**
 * UserRoles
 * @version		$Id$
 * @package		Model

 */

require_once dirname(__FILE__) .'/Base/UserRolesBase.php';
class UserRoles extends \UserRolesBase {

    const GET_ID_ROLE_PURCHASE_ORDER = 9;

    public function init() {
        parent::init();
        $this->attachBehavior(
            'TimeStamp', new \Flywheel\Model\Behavior\TimeStamp(), array(
                'create_attr' => 'assign_time',
            )
        );
    }

    public static function getRoles($conditions = array(),$order = '', $group = '', $cols = null){

        $query = \UserRoles::read();
        if(!empty($conditions)){
            foreach ($conditions as $condition){
                $query->andWhere($condition);
            }
        }
        if($order!='') $query->orderBy($order);
        if($group!='') $query->groupBy($group);

        if($cols!=null && is_string($cols)) $query->select($cols);
        return $query->execute()->fetchAll();

    }

    /**
     * Get Total number User By Roles id - Quyền
     * @param $roles_id
     * @return int
     */
    public static function getTotalUserByRoles($roles_id){
        $total = UserRoles::read()->select("count(*) as total")->andWhere("role_id={$roles_id}")->execute()->fetch();

        $total = isset($total['total']) ? intval($total['total']) : 0;

        return $total;
    }

    /**
     * Get User By Roles
     * @param $role_id
     * @param bool $assoc
     * @param bool $active
     * @return array
     */
    public static function getUserByRoles($role_id, $assoc = false, $active = false){
        $userRoles = UserRoles::findByRoleId($role_id);
        $users = array();
        if(!empty($userRoles)){
            foreach ($userRoles as $member) {
                if(!($member instanceof \UserRoles)){
                    continue;
                }

                $u = \Users::retrieveById($member->getUserId());

                if ($u) {
                    if ($assoc) {
                        $users[$member->getUserId()] = $u;
                    } else {
                        $users[] = $u;
                    }
                }
            }
        }

        return $users;
    }

    /**
     * Get Roles By User -- Quyen
     * @param Users $user
     * @return string
     */
    public static function getRolesByUser(\Users $user){
        if($user instanceof \Users){
            $user_roles = UserRoles::findByUserId($user->getId());
            $roles_list = array();
            if($user_roles instanceof UserRoles){
                $roles = Roles::retrieveById($user_roles->getRoleId());
                $roles_list[] = $roles;
            }
            return $roles_list;
        }
        return array();
    }


}