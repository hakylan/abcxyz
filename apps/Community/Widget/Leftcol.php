<?php
use Flywheel\Base;
use Flywheel\Factory;
use Flywheel\Controller\Widget;

class Leftcol extends Widget
{
    public $categories;
    public $featured_posts;
    public function begin() {

        $this->viewPath = Base::getApp()->getController()->getTemplatePath() .DIRECTORY_SEPARATOR .'Widget' .DIRECTORY_SEPARATOR;
        $this->viewFile = "Leftcol";
    }
    public function end()
    {
        return $this->render(array(
            'categories' => $this->categories,
            'featured_posts' => $this->featured_posts
        ));
    }
}