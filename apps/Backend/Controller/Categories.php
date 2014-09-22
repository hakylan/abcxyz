<?php

namespace Backend\Controller;

class Categories extends BackendBase
{

    //List users
    public function executeDefault()
    {

        $categories = \CommunicationPostCategories::findAll();
        $this->setView('Categories/default');
        $this->view()->assign(array('categories' => $categories));
        $this->document()->title = 'Danh mục';
        return $this->renderComponent();
    }

    public function executeAdd()
    {
        $cat = new \CommunicationPostCategories();

        if ($this->request()->isPostRequest()) {

            $title = ucwords(trim($this->request()->post('title')));
            $slug = strtolower($this->request()->post('slug'));
            $desc = trim($this->request()->post('description'));
            $order = trim($this->request()->post('order'));


            if ($this->_checkSlug($slug)) {
                \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Alias đã tồn tại');
                $this->redirect($this->createUrl('categories/add'));
            }
            if (!is_numeric($order)) {
                \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Order yêu cầu là số tự nhiên');
                $this->redirect($this->createUrl('categories/add'));
            }

            $cat->title = $title;
            $cat->slug = $slug;
            $cat->post_count = 0;
            $cat->order = $order;
            $cat->description = $desc;


            if ($cat->save()) {
                \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Đã tạo category mới');
                $this->redirect($this->createUrl('categories'));
            }
        }
        $this->document()->title = 'Thêm danh mục';
        $this->setView('Categories/add');
        return $this->renderComponent();
    }

    public function executeEdit()
    {
        $id = $this->request()->get('id');
        $category = \CommunicationPostCategories::retrieveById($id); //print_r($data);
        if(!$category->id){
            \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Danh mục không tồn tại');
            $this->redirect($this->createUrl('categories'));
        }

        if ($this->request()->isPostRequest()) {
            $title = ucwords(trim($this->request()->post('title')));
            $slug = strtolower($this->request()->post('slug'));
            $desc = trim($this->request()->post('description'));
            $order = trim($this->request()->post('order'));
            //echo t($id);
            //echo $this->_checkSlug($slug);
            //var_dump($this->_checkSlug($slug) != $id);die;
            if ($this->_checkSlug($slug) != $id) {
                \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Alias đã tồn tại');
                $this->redirect($this->createUrl('categories/edit/'.$id));
            }
            if (!is_numeric($order)) {
                \Flywheel\Session\Session::getInstance()->setFlash('msg', 'Order yêu cầu là số tự nhiên');
                $this->redirect($this->createUrl('categories/edit/'.$id));
            }

            $category->title = $title;
            $category->slug = $slug;
            $category->post_count = 0;
            $category->order = $order;
            $category->description = $desc;

            $category->save();
            $this->redirect($this->createUrl('categories'));
        }


        $this->setView('Categories/edit');
        $this->view()->assign(array(
            'category' => $category
        ));
        return $this->renderComponent();
    }

    public function executeDelete()
    {
        $id = $this->request()->get('id');
        $category = \CommunicationPostCategories::retrieveById($id); //print_r($data);
        $category->delete();
        $this->redirect($this->createUrl('categories'));
    }

    private function _checkSlug($slug)
    {
        $c = \CommunicationPostCategories::retrieveBySlug($slug);
        if ($c->id) {
            return $c->id;
        }
        return false;
    }

    private function checkTitle($title)
    {
        $c = \CommunicationPostCategories::retrieveByTitle($title);
        if ($c->title != $title) {
            return $c->id;
        }
        return false;
    }

}
