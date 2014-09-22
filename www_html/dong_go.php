<?php
require realpath('../bootstrap.php');
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../apps/Home/Config/main.cfg.php');
$env = \Flywheel\Base::ENV_DEV;
$app = \Flywheel\Base::createWebApp($config, $env, true);
$order_services = \OrderService::read()->andWhere("service_id=6")->orderBy("id","desc")->execute()
    ->fetchAll(\PDO::FETCH_CLASS,\OrderService::getPhpName(),array(null,false));
?>
<html>
<head>
    <title>Đơn hàng chọn dịch vụ Đóng gỗ</title>
    <meta charset="utf-8">
</head>
<body>
<style>
    .tg  {border-collapse:collapse;border-spacing:0;}
    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:8px 4px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:bold;padding:8px 4px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
</style>
<h2>Thống kê đơn hàng chọn dịch vụ đóng gỗ</h2>
<table style="width: 99%;text-align: center" class="tg">
    <tr>
        <th>STT</th>
        <th>Khách hàng</th>
        <th>Đơn hàng</th>
        <th>Trạng thái</th>
        <th>Thời gian mua hàng</th>
        <th>Thời gian nhận hàng từ người bán</th>
        <th>Thời gian chọn dịch vụ</th>
        <th>Kho hiện tại</th>
        <th>Kho đích</th>
        <th>Trạng thái kho</th>
        <th>Thời gian kho</th>
    </tr>
    <?php
    foreach ($order_services as $key=>$service) {
        if(!$service instanceof \OrderService){
            continue;
        }
        $order = \Order::retrieveById($service->getOrderId());
        if(!$order instanceof \Order){
            continue;
        }
        if($order->getIsDeleted() == 1 || $order->getStatus() == \Order::STATUS_OUT_OF_STOCK){
            continue;
        }
        $user = $order->getBuyer();
        if(!$user instanceof \Users){
            continue;
        }
        $time = new DateTime($service->getCreatedTime());
        $time_bought = new DateTime($order->getBoughtTime());

        ?>
        <tr style="border-bottom: 1px solid">
            <td><?php echo $key ?></td>
            <td><?php echo $user->getUsername() ?></td>
            <td><?php echo $order->getCode() ?></td>
            <td><?php echo $order->getStatusTitle() ?></td>
            <td><?php
                if(strtotime($order->getBoughtTime()) > 0){
                    echo $time_bought->format("H:i d-m-Y");
                }else{
                    echo "...";
                }
                ?>
            </td>
            <td><?php
                if(strtotime($order->getReceivedFromSellerTime()) > 0){
                    $time_receive = new DateTime($order->getReceivedFromSellerTime());
                    echo $time_receive->format("H:i d-m-Y");
                }else{
                    echo "...";
                }
                ?>
            </td>
            <td><?php echo $time->format("H:i d-m-Y") ?></td>
            <td><?php echo $order->getCurrentWarehouse() ?></td>
            <td><?php echo $order->getDestinationWarehouse() ?></td>
            <td><?php echo $order->getWarehouseStatusTitle()  ?></td>
            <td><p>Nhập kho:
                    <?php
                    if(strtotime($order->getWarehouseInTime()) > 0){
                        $time = new DateTime($order->getWarehouseInTime());
                        echo $time->format("H:i d-m-Y");
                    }else{
                        echo "...";
                    }
                    ?>
                </p>
                <p>Xuất kho:
                    <?php
                    if(strtotime($order->getWarehouseOutTime()) > 0){
                        $time = new DateTime($order->getWarehouseOutTime());
                        echo $time->format("H:i d-m-Y");
                    }else{
                        echo "...";
                    }
                    ?>
                </p>
            </td>
        </tr>
    <?php } ?>
</table>
</body>
</html>