<?php
require realpath('../bootstrap.php');
$globalCnf = require ROOT_PATH . '/config.cfg.php';
$config = array_merge( $globalCnf, require __DIR__ . '/../apps/Home/Config/main.cfg.php');
$env = \Flywheel\Base::ENV_PRO;
if ($env == \Flywheel\Base::ENV_DEV) {
    restore_error_handler();
    restore_exception_handler();
}
$app = \Flywheel\Base::createWebApp($config, $env, true);

$user_list = \OrderPeer::getUserFromOrderInit();

?>
<html>
<head>
    <title>Những khách hàng có những đơn hàng chưa đặt cọc</title>
    <meta charset="utf-8">
</head>
<body>
<style>
    .tg  {border-collapse:collapse;border-spacing:0;}
    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:8px 4px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:bold;padding:8px 4px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
</style>
<h2>Thống kê khách hàng có những đơn hàng chưa đặt cọc</h2>
<table style="width: 99%;text-align: center" class="tg">
    <tr>
        <th>STT</th>
        <th>Tài khoản</th>
        <th>Mã khách</th>
        <th>Fullname</th>
        <th>Địa chỉ</th>
        <th>Email</th>
        <th>SĐT</th>
        <th>STATUS</th>
        <th>Mobile Status</th>
        <th>Lần đặt hàng cuối</th>
        <th>Danh sách các mã đơn</th>
    </tr>
    <?php
    $key = 0;
    foreach ($user_list as $info) {
        $user = isset($info["user"]) ? $info["user"] : array();
        $orders = isset($info["order"]) ? $info["order"] : array();
        $order_end = end($orders);
        if(!$user instanceof \Users){
            continue;
        }
        $key++;
        ?>
        <tr style="border-bottom: 1px solid">
            <td> <?php echo $key ?>
            </td>
            <td><a href="<?php echo \SeuDo\Main::getBackendUrl()."user/detail/{$user->getId()}" ?>"
                   target="_blank"><?php echo $user->getUsername() ?></a>
            </td>
            <td><?php echo $user->getCode() ?></td>
            <td><?php echo $user->getFullName() ?></td>
            <td><?php
                echo $user->getDetailAddress();
                ?>
            </td>
            <td><?php
                echo $user->getEmail();
                ?>
            </td>
            <td><?php echo $user->getOneMobileUsing() ?></td>
            <td><?php echo $user->getStatus() ?></td>
            <td><?php echo $user->getVerifyMobile() == 1 ? "Active" : "Inactive" ?></td>
            <td><?php echo isset($order_end["time"]) ? Common::validDateTime($order_end["time"],"H:i d/m/Y")
                : "...";
                ?></td>
            <td>
                <?php
                if(!empty($orders)){
                    foreach ($orders as $order) { ?>

                        <a target="_blank" href="<?php echo \SeuDo\Main::getBackendUrl()."order/detail/default/{$order["id"]}"; ?>">
                            <?php echo $order["code"] ?>
                        </a><br/>
                <?php
                    }
                }
                ?>
            </td>
        </tr>
    <?php } ?>
</table>
</body>
</html>