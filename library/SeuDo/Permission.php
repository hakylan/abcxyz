<?php
namespace SeuDo;
use Flywheel\Acl\Acl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

class Permission {
    protected $_acl;
    public static $instance;

    public function __construct(){
        $this->_acl = new Acl();
    }

    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function addInstance($instance) {
        self::$instance = $instance;
    }

    public function init($data) {
        //roles
        $roles = array_keys($data);
        $this->setRoles($roles);

        //resource
        $resources = array();
        foreach ($data as $resource) {
            if(is_array($resource)) {
                foreach ($resource as $sub_resource) {
                    array_push($resources, $sub_resource);
                }
            }else{
                array_push($resources, $resource);
            }
        }
        $this->setResources($resources);

        //set access
        $this->setAccess($data);

        self::addInstance($this);

    }
    public function setRoles($roles) {
        for ($i = 0; $i < sizeof($roles); $i++) {

            if( !$this->_acl->hasRole(new GenericRole($roles[$i])) )
                $this->_acl->addRole(new GenericRole($roles[$i]));
        }
        return $this->_acl;
    }

    public function setResources($resources) {
        for ( $i= 0; $i< sizeof($resources); $i++) {
            if( !$this->_acl->hasResource($resources[$i]))
                $this->_acl->addResource(new GenericResource($resources[$i]));
        }
        return $this->_acl;
    }

    public function setAccess($data){

        foreach ($data as $role => $resource) {
            $this->_acl->allow($role, $resource);
        }
        return $this->_acl;
    }

    public function setDenied($resource, $roles = null) {
        if (null == $roles) {
            $roles = $this->_acl->getRoles();
        }

        for ($i = 0; $i< sizeof($roles); $i++) {
            $this->_acl->deny($roles[$i], $resource);
        }
    }

    public function isAllowed($resource) {
        if(!$this->_acl->hasResource($resource)) return false;

        $roles = $this->_acl->getRoles();
        $is_allowed = false;
        for ($i = 0; $i< sizeof($roles); $i++) {
            if($this->_acl->isAllowed($roles[$i], $resource) === true) {
                $is_allowed = true;break;
            }
        }
        return $is_allowed;

    }
}