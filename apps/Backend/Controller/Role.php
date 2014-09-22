<?php
namespace Backend\Controller;
use Flywheel\Base;
use Flywheel\Db\Type\DateTime;
use Flywheel\Factory;
use Flywheel\Config\ConfigHandler;
use Flywheel\Session\Session;
use SeuDo\Main;

class Role extends BackendBase {
    public function executeDefault() {
        if (!$this->isAllowed(PERMISSION_ROLE_VIEW)) {
            $this->raise403(self::t("Bạn không có quyền truy cập khu vực này!"));
        }

        $this->setView('Role/default');
        $roles = \Roles::read()->orderBy('label')
            ->execute()
            ->fetchAll(\PDO::FETCH_CLASS, \Roles::getTableName(), array(null, false));

        $countMemberStmt = \UserRoles::read()
            ->select("COUNT('id') AS ROWS, `role_id`")
            ->groupBy('role_id')
            ->execute();

        $count = array();
        while ($data = $countMemberStmt->fetch(\PDO::FETCH_ASSOC)) {
            $count[$data['role_id']] = $data['ROWS'];
        }

        $this->view()->assign('roles', $roles);
        $this->view()->assign('permissions', \Permissions::$permissions);
        $this->view()->assign('count', $count);

        return $this->renderComponent();
    }

    public function executeAdd() {
        if (!$this->request()->isPostRequest()) {
            Base::end();
        }

        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_ROLE_EDIT)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        $error = array();
        $r = new \Roles();
        if ($this->_save($r, $error)) {
            $this->dispatch('afterAddNewRole', new \BackendEvent($this, array('role' => $r)));
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->role = $r->toArray();
            $ajax->detail_link = $this->createUrl('role/detail', array('id' => $r->getId()));
            $ajax->message = self::t("Lưu nhóm thành công");
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Lưu nhóm không thành công");
            $ajax->error = $error;
        }

        return $this->renderText($ajax->toString());
    }

    public function executeEdit() {
        $this->validAjaxRequest();
        $ajax = new \AjaxResponse();
        if (!$this->request()->isPostRequest()) {
            Base::end();
        }

        if (!$this->isAllowed(PERMISSION_ROLE_EDIT)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        $id = $this->post('role_id', 'INT', 0);
        if (!$id || !($role = \Roles::retrieveById($id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t('Nhóm không tồn tại');
            return $this->renderText($ajax->toString());
        }

        $error = array();
        if ($this->_save($role, $error)) {
            $this->dispatch('onUpdateRoleData', new \BackendEvent($this, array(
                'role' => $role
            )));
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = self::t('Lưu Thành Công');
            $ajax->role = $role->toArray();
        } else {
            $ajax->message = self::t('Có lỗi xảy ra');
            $ajax->error = $error;
        }

        return $this->renderText($ajax->toString());
    }

    public function executeDetail() {
        $role = null;
        $error = array();
        $members = array();
        $cranes = array();
        $otherGuy = array();

        if (!$this->isAllowed(PERMISSION_ROLE_VIEW)) {
            $this->raise403(self::t('Bạn không có quyền vào khu vực này'));
        }

        $id = $this->get('id', 'INT', 0);
        if (!$id || !($role = \Roles::retrieveById($id))) {
            $error['not_found'] = self::t("Không tìm thấy nhóm");
        }

        if ($role) {
            //get Users in role
            $members = \UserRoles::getUserByRoles($role->getId(), true, true);
            $cranes = \Users::findBySection(\Users::SECTION_CRANE);

            foreach ($cranes as $crane) {
                if (!isset($members[$crane->getId()])) {
                    $otherGuy[$crane->getId()] = $crane;
                }
            }

            //God is here
            unset($members[1]);
            unset($otherGuy[1]);

            $assigned = \Permissions::findByRoleId($role->getId(), true);
            $permissions = \Permissions::$permissions;
            $temp = array();
            //manipulate its
            foreach($permissions as $group => &$pg) {
                foreach($pg['permissions'] as $permission => &$detail) {
                    if (isset($assigned[$permission])) {
                        $detail['assigned'] = true;
//                        $permissions[$group]['permissions'][$permission]['assigned'] = true;
                    } else {
//                        $permissions[$group]['permissions'][$permission]['assigned'] = false;
                        $detail['assigned'] = false;
                    }
                }
            }

            $this->view()->assign(array(
                'permissions' => $permissions
            ));
        }

        $this->setView('Role/detail');
        $this->view()->assign(array(
            'role' => $role,
            'members' => $members,
            'otherGuy' => $otherGuy,
            'error' => $error
        ));

        return $this->renderComponent();
    }

    public function executeRemove() {
        $this->validAjaxRequest();
        $id = $this->post('id', 'INT', 0);

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_ROLE_EDIT)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        if (!$id || !($r = \Roles::retrieveById($id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Không tìm thấy nhóm");
            return $this->renderText($ajax->toString());
        }

        $users = \UserRoles::getUserByRoles($r->getId());
        if (!empty($users)) {
            $ajax->type = \AjaxResponse::WARNING;
            $ajax->message = self::t("Nhóm vẫn còn thành viên, không thể xóa");
            return $this->renderText($ajax->toString());
        }

        if ($r->delete()) {
            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = self::t("Đã xóa nhóm {$r->getLabel()}");
        } else {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Lưu nhóm không thành công");
        }

        return $this->renderText($ajax->toString());
    }

    public function executeAddMember() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_ROLE_EDIT)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        $user_id = $this->post('user_id', 'INT', 0);
        if (!$user_id || !($user = \Users::retrieveById($user_id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Không có tài khoản này");
            return $this->renderText($ajax->toString());
        }

        $role_id = $this->post('role_id', 'INT', 0);
        if (!$role_id || !($role = \Roles::retrieveById($role_id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Không có nhóm này");
            return $this->renderText($ajax->toString());
        }

        if (\Users::SECTION_CRANE != $user->getSection()) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Tài khoản %username% không là nhân viên", array(
                "%username%" => $user->getUsername()
            ));
            return $this->renderText($ajax->toString());
        }

        if (!($userRole = \UserRoles::findOneByRoleIdAndUserId($role->getId(), $user->getId()))) {
            $userRole = new \UserRoles();
            $userRole->setRoleId($role->getId());
            $userRole->setUserId($user->getId());
            $userRole->save();
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = self::t("Thêm tài khoản vào nhóm thành công");
        $ajax->user = $user->toArray();
        $ajax->role = $role->toArray();

        return $this->renderText($ajax->toString());
    }

    public function executeRemoveMember() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_ROLE_EDIT)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        $user_id = $this->post('user_id', 'INT', 0);
        //not need check user

        $role_id = $this->post('role_id', 'INT', 0);
        if (!$role_id || !($role = \Roles::retrieveById($role_id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Không có nhóm này");
            return $this->renderText($ajax->toString());
        }

        if (($userRole = \UserRoles::findOneByRoleIdAndUserId($role->getId(), $user_id))) {
            $userRole->delete();
        }

        $ajax->type = \AjaxResponse::SUCCESS;
        $ajax->message = self::t("Xóa tài khoản khỏi nhóm thành công");
        $ajax->user_id = $user_id;
        $ajax->role = $role->toArray();

        return $this->renderText($ajax->toString());
    }

    /**
     * @param \Roles $role
     * @param $error
     * @return bool
     */
    protected function _save(\Roles $role, &$error) {
        $data = $this->post('input', 'ARRAY', array());
        $data['label'] = trim($data['label']);
        $data['description'] = trim($data['description']);
        $role->hydrate($data);
        if (!$role->getLabel()) {
            $error['role-label'] = self::t("Tên nhóm không được để trống!");
        }

        if (empty($error)) {
            if($role->save()) {
                return true;
            } else {
                foreach($role->getValidationFailures() as $validationFailure) {
                    $error[str_replace('.', '-', $validationFailure->getColumn())] = $validationFailure->getMessage();
                }
            }
        }

        return false;
    }

    public function executeChangePermissions() {
        $this->validAjaxRequest();

        $ajax = new \AjaxResponse();

        if (!$this->isAllowed(PERMISSION_ROLE_PERMISSION_MANAGE)) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Bạn không có quyền thực hiện thao tác này");
            return $this->renderText($ajax->toString());
        }

        $role_id = $this->post('role_id', 'INT', 0);
        if (!$role_id || !($role = \Roles::retrieveById($role_id))) {
            $ajax->type = \AjaxResponse::ERROR;
            $ajax->message = self::t("Không có nhóm này");
            return $this->renderText($ajax->toString());
        }

        $setting = $this->post('permissions', 'ARRAY', array());

        $role->beginTransaction();
        try {
            //current role's permission
            $assigned = \Permissions::findByRoleId($role->getId(), true);
//            var_export(array_keys($assigned));
//            var_export(array_keys($setting)); exit;
            $cancellation = array_diff_key($assigned, $setting);
            foreach ($setting as $permission => $status) {
                if (isset($assigned[$permission]) && ($status != $assigned[$permission])) {
                    //if existed permission record and toggled status. Never go in this case
                    if ($status) {//set on, update
                        $assigned[$permission]->setOn($status);
                        $assigned[$permission]->save(false);
                    } else {//off, remove
                        $assigned[$permission]->delete();
                    }
                } else if ($status) {//new permission
                    $om = new \Permissions();
                    $om->setRoleId($role->getId());
                    $om->setCode($permission);
                    $om->setOn(1);
                    $om->save();
                    $assigned[$om->getCode()] = $om;
                }
            }

            //remove cancellation permission
            foreach ($cancellation as $cp) {
                $cp->delete();
            }

            $ajax->type = \AjaxResponse::SUCCESS;
            $ajax->message = self::t("Thay đổi quyền của nhóm thành công");

            $role->commit();
            return $this->renderText($ajax->toString());
        } catch (\Exception $e) {
            $role->rollBack();
            throw $e;
        }
    }
}