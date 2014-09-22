<?php

namespace Backend\Controller;

class Location extends BackendBase {

    //List users
    public function executeDefault() {
        $menu = array();
        /*
          $menu[]=array('id'=>0,
          'label'=>'MENU',
          'parent_id'=>-1,
          'level'=>0
          ); */
        $menu = $this->DeQuy(0, $menu, -1, $option = array('type' => 'default'));

        $this->setView('Location/default');
        $this->view()->assign(array('menu' => $menu));
        return $this->renderComponent();
    }

    public function executeAdd() {
        $a = new \Locations();
        $q = \Locations::read()
                ->where('`parent_id` = :id ')
                ->setParameter(':id', 0, \PDO::PARAM_STR)
                ->execute()
                ->fetchAll(\PDO::FETCH_CLASS, \Locations::getPhpName(), array(null, false));


        $menu = array();
        foreach ($q as $key => $value) {
            $menu[] = array('id' => $value->getId(),
                'label' => $value->getLabel(),
                'parent_id' => $value->getParentId()
            );
            # code...
        }
        $menu = array();
        /* $menu[]=array('id'=>0,
          'label'=>'MENU',
          'parent_id'=>-1,
          'level'=>0
          ); */
        $option = array('type' => 'add', 'id' => 100);
        $menu = $this->DeQuy(0, $menu, -1, $option);
       
        $location = new \Locations();
        if ($this->request()->isPostRequest()) {

            if ('' != $parent_id = $this->request()->post('category')) {
                //echo $parent_id;
                $location->setParentId($parent_id);
                $const = array('COUNTRY' => 1, 'STATE' => 2, 'DISTRICT' => 3, 'phuong' => 4);
                $invert = array(1 => 'COUNTRY', 2 => 'STATE', 3 => 'DISTRICT', 4 => 'phuong');
                if ($parent_id == 0) {
                    $location->setType('COUNTRY');
                } else {
                    $a = \Locations::retrieveById($parent_id);
                    $x = $a->getType();
                    $tg = $const[$x] + 1;
                    $type = $invert[$tg];
                    $location->setType($type);
                }
            };

            if ('' != $label = $this->request()->post('label')) {
                $location->setLabel($label);
            };

            if ('' != $ordering = $this->request()->post('ordering')) {
                $location->setOrdering($ordering);
            };

            $location->setStatus('');
            $location->setCanDelete(1);
            $location->setCODE('');

            $location->save();
            $this->redirect($this->createUrl('Location/'));
        }
        $this->setView('Location/add');
        $this->view()->assign(array('menu' => $menu));
        return $this->renderComponent();
        return $this->renderText("This controller list add test");
    }


    public function Dequy($id, $menu, $level, $option) {
        $level = $level + 1;
        $q = \Locations::read()
                ->where('`parent_id` = :id ')
                ->setParameter(':id', $id, \PDO::PARAM_STR)
                ->execute()
                ->fetchAll(\PDO::FETCH_CLASS, \Locations::getPhpName(), array(null, false));
        foreach ($q as $key => $value) {
            if ($option['type'] == 'edit' && $option['id'] == $value->getId()) {  //donothing
            } else {
                $menu[] = array('id' => $value->getId(),
                    'label' => $value->getLabel(),
                    'parent_id' => $value->getParentId(),
                    'level' => $level
                );
                $menu = $this->DeQuy($value->getId(), $menu, $level, $option);
            }
        }
        return $menu;
    }

    public function executeEdit() {
        $id = $this->request()->get('id');
        $location = \Locations::retrieveById($id); //print_r($data);
        $menu = array();
        /* $menu[]=array('id'=>0,
          'label'=>'MENU',
          'parent_id'=>-1,
          'level'=>0
          ); */

        $option = array('type' => 'edit', 'id' => $location->getId());
        $menu = $this->DeQuy(0, $menu, -1, $option);

        if ($this->request()->isPostRequest()) {

            if ('' != $parent_id = $this->request()->post('category')) {
                //echo $parent_id;
                $location->setParentId($parent_id);
                $const = array('COUNTRY' => 1, 'STATE' => 2, 'DISTRICT' => 3, 'phuong' => 4);
                $invert = array(1 => 'COUNTRY', 2 => 'STATE', 3 => 'DISTRICT', 4 => 'phuong');
                if ($parent_id == 0) {
                    $location->setType('COUNTRY');
                } else {
                    $a = \Locations::retrieveById($parent_id);
                    $x = $a->getType();
                    $tg = $const[$x] + 1;
                    $type = $invert[$tg];
                    $location->setType($type);
                }
            };

            if ('' != $label = $this->request()->post('label')) {
                $location->setLabel($label);
            };

            if ('' != $ordering = $this->request()->post('ordering')) {
                $location->setOrdering($ordering);
            };

            $location->setStatus('');
            $location->setCanDelete(1);
            $location->setCODE('');

            $location->save();
            $this->redirect($this->createUrl('Location'));
        }


        $this->setView('Location/edit');
        $this->view()->assign(array('menu' => $menu,
            'data' => $location
        ));
        return $this->renderComponent();
    }

    public function executeDelete() {
        $id = $this->request()->get('id');
        $this->DeleteDequy($id);
        $location = \Locations::retrieveById($id); //print_r($data);
        $location->delete();
        $this->redirect($this->createUrl('Location'));
    }

    public function DeleteDequy($id) {
        $q = \Locations::read()
                ->where('`parent_id` = :id ')
                ->setParameter(':id', $id, \PDO::PARAM_STR)
                ->execute()
                ->fetchAll(\PDO::FETCH_CLASS, \Locations::getPhpName(), array(null, false));
        foreach ($q as $key => $value) {
            $this->DeleteDequy($value->getId());
            $value->delete();
        }
    }

}
