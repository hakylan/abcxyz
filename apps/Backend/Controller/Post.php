<?php

namespace Backend\Controller;

use \Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Exception;
use \SeuDo\SFS\Client;
use Flywheel\Paginator\Paginator as Paginator;
class Post extends BackendBase
{
    public function executeDefault()
    {
        //$currentUser = \BackendAuth::getInstance()->getUser();
        $categories = \CommunicationPostCategories::findAll();
        $cats = array();
        foreach ($categories AS $cat) {
            $cats[$cat->id] = $cat->title;
        }


        $page = $this->request()->get('page');
        $page = $page ? $page : 1;
        $limit = 10;
        $totalPosts = \CommunicationPosts::countAll();
        $pageUrl = $this->createUrl('home') . '/';
        $paging = new Paginator($page, $totalPosts, $limit, $pageUrl, '/');
        $offset = $paging->startItem();
        $posts = \CommunicationPosts::getPosts($limit, $offset);


        $this->setView('Post/default');
        $this->view()->assign(array('posts' => $posts,
            'cats' => $cats,
            'paging' => $paging
        ));
        return $this->renderComponent();
    }

    public function executeAdd()
    {
        $categories = \CommunicationPostCategories::findAll();
        $post = new \CommunicationPosts();

        if ($this->request()->isPostRequest()) {

            $post->title = trim($this->request()->post('title'));
            $post->category_id = trim($this->request()->post('category'));
            $post->user_id = \BackendAuth::getInstance()->getUser()->id;
            $alias = trim($this->request()->post('alias'));
            $post->alias = $alias ? $alias : $this->_url_title($post->title);
            $post->content = trim($this->request()->post('content'));

            $images = $this->request()->post('images');
            $imgs = '';
            if ($images) {
                $images = explode(',', $images);

                $sfs = \SeuDo\SFS\Client::getInstance();
                $uploader = new \SeuDo\SFS\Upload('post');
                foreach ($images AS $img) {
                    $src = ConfigHandler::get('temp_upload_dir') . $img;
                    //$to = ConfigHandler::get('post_upload_dir') . $img;
                    $to = uniqid() . '_' . time() . '.jpg';

                    $uploader->setFile($src);
                    $uploader->setFileName($to);
                    if (!$sfs->upload($uploader)) {
                        print_r($sfs->getHttpCode());die;
                    }
                    if ($sfs->getHttpCode() == 200) {
                        $result = json_decode($sfs->getResponse());
                        $imgs .= $result->file . ',';
                    }
                }

            }
            $post->images = $imgs;
            $created_time = time();

            $post->created_date = gmdate('Y-m-d', $created_time);
            $post->modified_date = gmdate('Y-m-d', $created_time);


            $rate_data = array(
                '1' => '0',
                '2' => '0',
                '3' => '0',
                '4' => '0',
                '5' => '0'
            );
            $post->rate_data = serialize($rate_data);

            $post->save();

            //update post count
            $cat = \CommunicationPostCategories::retrieveById($post->category_id);
            $cat->post_count = $cat->post_count + 1;
            $cat->save();


            $this->redirect($this->createUrl('post'));
        }
        $this->setView('Post/add');
        $this->view()->assign(array('categories' => $categories));
        return $this->renderComponent();
    }

    public function executeEdit()
    {
        $id = $this->request()->get('id');

        $post = \CommunicationPosts::retrieveById($id);
        $categories = \CommunicationPostCategories::findAll();


        $ncatid = trim($this->request()->post('category'));
        if ($this->request()->isPostRequest()) {

            if ($ncatid != $post->category_id) {
                $cato = \CommunicationPostCategories::retrieveById($ncatid);
                $cato->post_count = $cato->post_count - 1;
                $cato->save();

                $catn = \CommunicationPostCategories::retrieveById($ncatid);
                $catn->post_count = $catn->post_count + 1;
                $catn->save();
            }

            $post->title = trim($this->request()->post('title'));
            $post->category_id = $ncatid;
            $alias = trim($this->request()->post('alias'));
            $post->alias = $alias ? $alias : $this->_url_title($post->title);
            $post->images = trim($this->request()->post('images'));
            $post->content = trim($this->request()->post('content'));
            if (empty($post->alias)) {
                $post->alias = $this->_url_title($post->title);
            }

            $created_time = time();

            $post->created_date = date('d-m-Y', $created_time);
            $post->modified_date = date('d-m-Y', $created_time);
            $post->save();


            $this->redirect($this->createUrl('post'));
        }


        $this->setView('Post/edit');
        $this->view()->assign(array(
            'post' => $post,
            'categories' => $categories
        ));
        return $this->renderComponent();
    }

    public function executeDelete()
    {
        $id = $this->request()->get('id');

        $post = \CommunicationPosts::retrieveById($id); //print_r($data);
        $post->delete();
        $this->redirect($this->createUrl('post'));
    }

    public function executeUpload()
    {
        $uploader = new \UploadHandler(array(
            'script_url' => $this->createUrl('post/upload'),
            'upload_dir' => ConfigHandler::get('temp_upload_dir'),
            'upload_url' => ConfigHandler::get('temp_upload_url')
        ));
    }

    public function executeDeleteFile()
    {
        $filename = $this->request()->get('file');
        $file = ConfigHandler::get('temp_upload_dir') . $filename;

        $postId = $this->request()->get('pid');


        if (file_exists($file)) {
            unlink($file);
        }
        if ($postId) {
            //$filep = ConfigHandler::get('post_upload_dir') . $filename;
            $post = \CommunicationPosts::retrieveById($postId);
            $post->images = str_replace(',' . $filename, '', $post->images);
            $post->save();
        }
        return $this->renderText('Success');
    }

    public function executeSaveUpload()
    {


    }


    private function _url_title($str, $separator = '-', $lowercase = FALSE)
    {
        if ($separator === 'dash') {
            $separator = '-';
        } elseif ($separator === 'underscore') {
            $separator = '_';
        }

        $q_separator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;' => '',
            '[^a-z0-9 _-]' => '',
            '\s+' => $separator,
            '(' . $q_separator . ')+' => $separator
        );

        $str = strip_tags($str);
        foreach ($trans as $key => $val) {
            $str = preg_replace('#' . $key . '#i', $val, $str);
        }

        if ($lowercase === TRUE) {
            $str = strtolower($str);
        }

        return strtolower(trim(trim($str, $separator)));
    }


}
