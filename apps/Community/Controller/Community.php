<?php
namespace Community\Controller;

use Flywheel\Factory;
use Community\Controller\CommunityBase;
use Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Paginator\Paginator as Paginator;

class Community extends CommunityBase
{

    public function executeDefault()
    {
        include_once LIBRARY_PATH . '/TextHelper.php';
        $cats = \CommunicationPostCategories::findAll();
        $ca = array();
        foreach ($cats AS $c) {
            $ca[$c->id]['title'] = $c->title;
            $ca[$c->id]['slug'] = $c->slug;
        }


        $featured_posts = \CommunicationPosts::getFeaturedPosts(5);


        $page = $this->request()->get('page');
        $page = $page ? $page : 1;
        $limit = 10;
        $totalPosts = \CommunicationPosts::countAll();
        $pageUrl = $this->createUrl('home') . '/';
        $paging = new Paginator($page, $totalPosts, $limit, $pageUrl, '/');
        $offset = $paging->startItem();
        $posts = \CommunicationPosts::getPosts($limit, $offset);


        $this->document()->title = 'Cộng đồng';
        $this->setView('Community/default');
        $this->view()->assign(array(
            'cats' => $cats,
            'ca' => $ca,
            'posts' => $posts,
            'featured_posts' => $featured_posts,
            'paging' => $paging,
        ));
        return $this->renderComponent();
    }

    public function executeCategory()
    {
        error_reporting(0);
        include_once LIBRARY_PATH . '/TextHelper.php';

        $slug = $this->request()->get('slug');
        $cat = \CommunicationPostCategories::retrieveBySlug($slug);
        $cats = \CommunicationPostCategories::findAll();
        $ca = array();
        foreach ($cats AS $c) {
            $ca[$c->id]['title'] = $c->title;
            $ca[$c->id]['slug'] = $c->slug;
        }

        $featured_posts = \CommunicationPosts::getFeaturedPosts(5);

        $page = $this->request()->get('page');
        $page = $page ? $page : 1;
        $limit = 10;
        $totalPosts = \CommunicationPosts::countAll();
        $pageUrl = $this->createUrl('category') . '/' . $slug . '/';
        $paging = new Paginator($page, $totalPosts, $limit, $pageUrl, '/');
        $offset = $paging->startItem();
        $posts = \CommunicationPosts::getByCategory($cat->id, $limit, $offset);


        $this->document()->title = 'Cộng đồng';
        $this->setView('Community/default');
        $this->view()->assign(array(
            'cats' => $cats,
            'ca' => $ca,
            'posts' => $posts,
            'featured_posts' => $featured_posts,
            'paging' => $paging,
        ));
        return $this->renderComponent();
    }

    public function executePost()
    {
        error_reporting(E_ERROR);

        $alias = $this->request()->get('alias');
        $post = \CommunicationPosts::retrieveByAlias($alias);

        if (!$post) {
            $this->redirect('');
        }
        $images = explode(',', $post->images);
        $imgs = array();
        foreach ($images AS $img) {
            $imgs[] = ConfigHandler::get('post_upload_url') . $img;
        }
        $cats = \CommunicationPostCategories::findAll();
        $ca = array();
        foreach ($cats AS $c) {
            $ca[$c->id]['title'] = $c->title;
            $ca[$c->id]['slug'] = $c->slug;
        }
        $featured_posts = \CommunicationPosts::getFeaturedPosts(5);
        $this->document()->title = $post->title . ' - Cộng đồng';
        $this->setView('Community/post');
        $this->view()->assign(array(
            'cats' => $cats,
            'ca' => $ca,
            'post' => $post,
            'images' => $imgs,
            'featured_posts' => $featured_posts
        ));
        return $this->renderComponent();
    }

    public function executeRate()
    {
        error_reporting(E_ERROR);
        $pId = $this->request()->post('pid');
        $val = $this->request()->post('p');
        //echo $pId;die;
        $currentUser = \CommunityAuth::getInstance()->getUser();
        if (!$currentUser) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Bạn vui lòng đăng nhập để đánh giá cửa hàng.')));
        }
        $uId = $currentUser->id;
        //$uId = 1;

        $post = \CommunicationPosts::retrieveById($pId);
        if (!$post) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Bài viết không tồn tại.')));
        }

        //list nguoi vote

        $voters = unserialize($post->rate_voters);
        if (array_key_exists($uId, $voters)) {
            return $this->renderText(json_encode(array('error' => 1, 'msg' => 'Bạn đã từng đánh giá cho cửa hàng.')));
        }
        $voter = array($uId => $val);
        $voters[$uId] = $voter;

        //var_dump($voters);die;

        $post->rate_voters = serialize($voters);
        $post->rate_score = $post->rate_score + $val;
        $post->rate_total = $post->rate_total + 1;
        $avg = $post->rate_score / $post->rate_total;
        $post->rate_avg = $avg;


        //so luong nguoi rate cho moi rank
        $data = unserialize($post->rate_data);
        $data[$val] = $data[$val] + 1;
        $post->rate_data = serialize($data);


        $post->save();
        return $this->renderText(json_encode(array('error' => 0, 'msg' => 'Cảm ơn bạn đã đánh giá cửa hàng này.')));


    }

    public function executeUpdateCommentCount()
    {
        $id = $this->request()->get('pid');
        $post = \CommunicationPosts::retrieveById($id);
        if (!$post) {
            return;
        }
        $post->comment_count = $post->comment_count + 1;
        $post->save();
        return $this->renderText('done');
    }
}
