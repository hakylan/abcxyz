<?php 
/**
 * AdminMenu
 *  This class has been auto-generated at 05/07/2013 16:49:46
 * @version		$Id$
 * @package		Model

 */
use Flywheel\Db\Manager;
use Flywheel\Model\ActiveRecord;
class ModelHelper {
    public $obj;
    static $instance;
    public  $condition = array(),
            $select = '*',
            $order_by = array(),
            $offset = 0,
            $maxresult = 0,
            $group_by = '',
            $key = '',
            $table = '';


    public function __construct($obj){
        $this->obj = $obj;
    }
    public static function getInstance($obj){

        return new self($obj);
    }
    public function addCondition($condition){
        $this->condition = array_merge_recursive($this->condition,array($condition));
        return $this;
    }



    public function get($config = array()){
        /*array(
            'condition'=>array(

            ),
            'key'=>'id',
        );*/
        if(!is_object($this->obj)) return false;
        $this->buildQuery($config);
        $object = $this->obj;

        $query = $object::read();

        if($this->table!=''){
            $query->from($this->table);
        }

        $query->select($this->select);

        if(!empty($this->condition)){
            $query->where(' 1 ');
            foreach ($this->condition as $cond){
                $query->andWhere($cond);
            }
        }
        if(!empty($this->order_by)){
            $query->orderBy($this->order_by[0],$this->order_by[1]);
        }

        if($this->maxresult >=0){
            $query->setFirstResult($this->offset);
            $query->setMaxResults($this->maxresult);
        }

        if($this->group_by != ''){
            $query->groupBy($this->group_by);
        }

        $datas = $query->execute()->fetchAll(\PDO::FETCH_CLASS, get_class($this->obj), array(null, false));
        if($this->key != ''){
            $new_return = array();
            if(!empty($datas)){
                foreach ($datas as $data){
                    $key = $this->key;
                    if($data)$new_return[$data->$key] = $data;
                }
            }
            return $new_return;
        }
        return $datas;
    }

    public function delete($config = array()){
        if(!$config || empty($config)) return false;
        if(!is_object($this->obj)) return false;
        $object = $this->obj;
        $query = $object::read();
        if(isset($config['condition'])){
            $query->where(' 1 ');
            $condition = $config['condition'];
            if( is_array($condition) ){
                foreach ($condition as $cond){
                    $query->andWhere($cond);
                }
            }else{
                $query->andWhere($condition);
            }
        }
        $query->delete($config['table'])->execute();
        return true;
    }

    public function buildQuery($config = array()){
        if(isset($config['select']) && $config['select']!=''){
            $this->select = $config['select'];
        }
        if(isset($config['condition']) && !empty($config['condition'])){
            foreach ($config['condition'] as $cond){
                $this->addCondition($cond);
            }
        }
        if(isset($config['order_by']) && $config['order_by']!=''){
            $p = explode(' ',$config['order_by']);
            $this->order_by = array($p[0],$p[1]);
        }
        if(isset($config['limit']) && $config['limit']!=''){
            $limit = explode(',',$config['limit']);
            $this->offset = $limit[0];
            $this->maxresult = $limit[1];
        }
        if(isset($config['group_by'])){
            $this->group_by = $config['group_by'];
        }
        if(isset($config['key'])){
            $this->key = $config['key'];
        }
    }
}