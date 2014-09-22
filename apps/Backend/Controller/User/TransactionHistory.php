<?php

namespace Backend\Controller\User;

use Backend\Controller\BackendBase;

class TransactionHistory extends BackendBase {
    public function executeDefault() {
        return $this->executeGetTransactions();
    }

    public function executeGetTransactions() {
        if (!$this->isAllowed(PERMISSION_USER_VIEW_FINANCE)) {
            return $this->renderText(\AjaxResponse::responseError(self::t("Bạn không có quyền truy cập chức năng này")));
        }

        $user_id = $this->get('user_id');
        $user = \Users::retrieveById($user_id);
        if (!$user) {
            return $this->renderText(\AjaxResponse::responseError(self::t("Tài khoản không tồn tại với id {$user_id}")));
        }

        $page = $this->get('page', 'INT', 1);
        $num_show = 36;

        $q = \UserTransaction::read()
            ->where('`user_id` = :user_id')
            ->setParameter(':user_id', $user->getId());

        $cq = clone $q;
        $q->setMaxResults($num_show)
            ->setFirstResult(($page - 1) * $num_show)
            ->orderBy('closed_time', 'DESC')
            ->addOrderBy('id', 'DESC');

        $stmt = $q->execute();

        $total = $cq->count('id')->execute();

        $transactions = [];
        while($r = $stmt->fetchObject(\UserTransaction::getPhpName(), array(null, false))) {
            /** @var \UserTransaction $r */
            if ($r->getTransactionNote() == '') {
                $detail = json_decode($r->getTransactionDetail(), true);
                if (isset($detail['note'])) {
                    $r->setTransactionNote($detail['note']);
                } elseif (isset($detail['detail'])) {
                    $r->setTransactionNote($detail['detail']);
                }
            }

            $temp = $r->toArray();
            $temp['transaction_type'] = \UserTransaction::$transaction_type[$r->getTransactionType()];
            if ($r->getObjectType() == \UserTransaction::OBJECT_TYPE_ORDER) {
                $temp['object_link'] = $this->createUrl('order/detail/default', array(
                    'id' => $r->getObjectId()
                ));
            } else {
                $temp['object_link'] = '';
            }
            $transactions[] = $temp;
        }

        $ajax = new \AjaxResponse();
        $ajax->total = $total;
        $ajax->transactions = $transactions;
        $ajax->page = $page;
        $ajax->page_size = $num_show;
        $ajax->type = \AjaxResponse::SUCCESS;
        return $this->renderText($ajax->toString());
    }
}