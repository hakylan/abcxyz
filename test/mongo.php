<?php
require_once '../bootstrap.php';
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../apps/Home/Config/main.cfg.php');
try {
    $app = \Flywheel\Base::createWebApp($config, \Flywheel\Base::ENV_DEV, true);
//    $qb = \SeuDo\MongoDB::getConnection('keyword_search');
//    $qb->insert('collectionName', [
//        'name'  =>  'Alex',
//        'age'   =>  22,
//        'likes' =>  ['whisky', 'gin']
//    ]);
//    var_dump(Users::setUserCode(Users::findOneById(25)));

    // dau's test OrderComment
    /* ADD */
//    $order_comment = new \mongodb\OrderComment();
//    $context = new \mongodb\OrderCommentResource\Chat("test chat");
//    $order_comment->setContext($context);
//
//    $order_comment->setOrderId(1);
//    $order_comment->setContent(null);
//    $order_comment->setOrderCode(2);
//    $order_comment->setScope(\mongodb\OrderComment::TYPE_INTERNAL);
//    $order_comment->setCreatedBy(1);
//
//    $order_comment->save();

//    $order_comment = \mongodb\OrderComment::findOneByOrderId(1);
//    $results = \mongodb\OrderComment::findByScope(\mongodb\OrderComment::TYPE_EXTERNAL, \mongodb\OrderCommentResource\BaseContext::TYPE_CHAT, 10);
//    $orders = \mongodb\OrderComment::findByScope(\mongodb\OrderComment::TYPE_EXTERNAL, BaseContext::TYPE_CHAT);
//    foreach ($results as $order_comment) {
//        if ($order_comment instanceof \mongodb\OrderComment) {
//            $context = $order_comment->getContext();
//            if ($context instanceof \mongodb\OrderCommentResource\Chat) {
//                var_dump($context->getMessage());
//            }
////        var_dump();
////            echo $order_comment->getId(), ' ', $order_comment->getCreatedBy(), ' ', $order_comment->getOrderId(), ' ', $order_comment->getContent();
//        }
//    }

//    $order_item_comment = new \mongodb\OrderItemComment();
//    $order_item_comment->setOrderId(1);
//    $order_item_comment->setItemId(2);
//    $order_item_comment->setMessage(" test ");
//    $order_item_comment->setCreatedBy(3);
//    $created_time = new MongoDate();
//    $order_item_comment->setCreatedTime($created_time);
//    $order_item_comment->save();
//
//    $results = \mongodb\OrderItemComment::findByItemId(1, 2);
////    var_dump($results);
//    foreach ($results as $item) {
//        if ($item instanceof \mongodb\OrderItemComment) {
//            var_dump($item->getId(), $item->getOrderId(), $item->getItemId(), $item->getMessage(), $item->getCreatedTime(), $item->getCreatedBy());
//        }
//    }

    $order_id = 10;
    $item_id = 23;
    $results = OrderItemComment::loadOrderItemComments($order_id, $item_id, -1, 3);

    print_r($results);

    var_dump('Finished');
} catch (\Exception $e) {
    //    Ming_Exception::printExceptionInfo($e);
    print_r($e);
}
