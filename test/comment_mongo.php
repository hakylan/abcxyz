<?php
require_once __DIR__ . '/../bootstrap.php';
\Flywheel\Config\ConfigHandler::import('root.config');

\Flywheel\Loader::import('root.model.*');

try {
    $comment = new \mongodb\OrderComment();
    $comment->setContext(new \mongodb\OrderCommentResource\Chat('Fucking'));
    $comment->save();

    $comment->setOrderId(1);
    $comment->setCreator('username');
    $comment->save();
} catch (\Exception $e) {
    print_r($e->getMessage());
}