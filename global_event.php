<?php
const ON_SUCCESS_ORDER = 'onSuccessOrder';
const ON_AFTER_CHANGE_ORDER_STATUS_BACKEND = 'onAfterChangeOrderStatusBackend';
const ON_CONFIRM_ORDER_BACKEND = 'onConfirmOrderBackend';
const ON_CHAT_ORDER_BACKEND = 'onChatOrderBackend';
const ON_AFTER_CHANGE_REAL_COD = 'onAfterChangeRealCod';
const ON_AFTER_CREATE_BILL = 'onAfterCreateBill';
\SeuDo\GlobalEventDispatcher::addEvents(array(
    ON_SUCCESS_ORDER
));
//listener change order status hanv
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND,
        array( new \SeuDo\Notification\NotificationUser(), 'beforeSaveNotificationTypeOrderStatus' ) );

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND,
        array(\SeuDo\SMS\CustomerSupport::getInstance(), 'onOrderChangeStatus'));

//listener change order (price,quantity)
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_CONFIRM_ORDER_BACKEND,
        array( new \SeuDo\Notification\NotificationUser(), 'beforeSaveNotificationTypeConfirmOrder' ) );

//listener chat order
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_CHAT_ORDER_BACKEND,
        array( new \SeuDo\Notification\NotificationUser(), 'beforeSaveNotificationTypeChatOrder' ));

// Transfer order logistic
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND,
        array( new OrderEvent(), 'transferOrderLogisticWhenBought' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAddFreightBill",
        array( new OrderEvent(), 'transferOrderLogisticWhenUpdateFreightBill' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onRemoveFreightBill",
        array( new OrderEvent(), 'transferOrderLogisticWhenUpdateFreightBill' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAfterChooseService",
        array( new OrderEvent(), 'transferOrderChooseServicesCPN' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAfterChooseServiceFront",
        array( new OrderEvent(), 'transferOrderChooseServicesCPN' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAfterChooseService",
        array( new OrderEvent(), 'transferOrderChooseServices' ));

//Log activity
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAfterChooseServiceFront",
        array( new OrderEvent(), 'logOrderCommentFrontend' ));
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CHANGE_ORDER_STATUS_BACKEND,
        array( new OrderEvent(), 'logOrderCommentWhenChangeStatus' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("onAfterChooseService",
        array( new OrderEvent(), 'logOrderCommentWhenChooseServices' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("afterChangeDomesticShippingFee",
        array( new OrderEvent(), 'logCommentWhenChangeDomesticShipping' ));

\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("afterCustomerConfirmed",
        array( new OrderEvent(), 'logOrderCommentWhenCustomerConfirmed' ));

// Thay đổi mã hóa đơn trên site gốc
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("afterChangeInvoice",
        array( new OrderEvent(), 'logOrderWhenChangeInvoice' ));

// Log comment khi nhập kho
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("orderStockIn",
        array( new OrderEvent(), 'logOrderWhenStockIn' ));
// Log comment khi xuat kho
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("orderStockOut",
        array( new OrderEvent(), 'logOrderWhenStockOut' ));


\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("afterEditUnitPriceItem",
        array( new OrderEvent(), 'activityItemWhenUpdate' ));


\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener("afterEditQuantityItem",
        array( new OrderEvent(), 'activityItemWhenUpdate' ));

//log activity  event create bill delivery
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CREATE_BILL,
        array( new DeliveryEvent(), 'logDeliveryCreateBill' ));
\SeuDo\GlobalEventDispatcher::getEventDispatcher()
    ->addListener(ON_AFTER_CHANGE_REAL_COD,
        array( new DeliveryEvent(), 'logChangeRealCod' ));



