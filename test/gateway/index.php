<?php
$response = '';
$apiUrl = @$_COOKIE['api_url'];
$appKey = @$_COOKIE['app_key'];
$appSecret = @$_COOKIE['app_secret'];
$message = '';
$short_code = '';
$phone = '';
$method = '';

$error = array();

if (isset($_POST['submit']) && $_POST['submit']) {
    $appKey = @$_POST['app_key'];
    setcookie('app_key', $appKey);

    $appSecret = @$_POST['app_secret'];
    setcookie('app_secret', $appSecret);

    $apiUrl = rtrim(trim(@$_POST['api_url']), '/');
    setcookie('api_url', $apiUrl);

    $message = trim(@$_POST['message']);
    $short_code = trim(@$_POST['short_code']);
    $phone = trim(@$_POST['phone']);
    $time = time();

    $signature = md5($message .$phone .$short_code .$time .$appSecret);

    $method = trim(@$_POST['method']);

    /*if (!$apiUrl || !$appKey || $appSecret) {
        $error['api'] = 'Điền các thông số API';
    }*/

    if (!$method) {
        $error['method'] = 'Chọn tác vụ';
    }

    if (!$phone) {
        $error['phone'] = 'Nhập số điện thoại';
    }

    if (!$short_code) {
        $error['short_code'] = 'Nhập đầu số';
    }

    if (!$message) {
        $error['message'] = 'Nhập SMS';
    }

    if (empty($error)) {
        $response = http($apiUrl .'/' .$method, 'POST', array(
                'consumer_key' => $appKey,
                'message' => $message,
                'shortcode' => $short_code,
                'phone' => $phone,
                'time' => $time,
                'signature' => $signature
            ));
    }
}


function http($url, $method, $parameters= null) {
//    $url = 'http://localhost/seudo/www_html/api/sms/gateway/verify_mobile';
    $ci = curl_init();
    /* Curl settings */

    curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ci, CURLOPT_HEADER, false);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    switch ($method) {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($parameters)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
            }
            break;
        case 'DELETE':
            if (!empty($parameters)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
            }
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
        case 'PUT':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "PUT");
            if (!empty($parameters)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
            }
            break;
        case 'GET':
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "GET");
            if (!empty($parameters)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $parameters);
            }
            break;
    }


    curl_setopt($ci, CURLOPT_URL, $url);
    $response = curl_exec($ci);
    $error = curl_errno($ci);
    $errorMess = curl_error($ci);
    $httpCode = curl_getinfo($ci, CURLINFO_HTTP_CODE);
    curl_close($ci);
    if ($error) {
        throw new \RuntimeException("CURL Error: {$errorMess}[{$error}]");
    }
    return $response;
}
?>

<style>
    body {
        font-size: 13px;
    }
    label {
        display: block;
    }

    label span {
        float: left;
        display: block;
        text-align: right;
        width: 100px;
        padding-right: 10px;
    }

    label input {
        width: 300px;
        height: 22px;
    }
</style>

<form action="" method="POST">
    <div style="width: 50%; float: left">
        <?php if (isset($error['api']) && $error['api']) : ?>
            <span style="color: red"><?php echo $error['api'] ;?></span>
        <?php endif; ?>
        <label>
            <span>API URL</span>
            <input name="api_url" width="50" value="<?php echo $apiUrl ?>">
        </label><br>

        <label>
            <span>Consumer key</span>
            <input name="app_key" width="50" value="<?php echo $appKey ?>">
        </label><br>

        <label>
            <span>Consumer Secret</span>
            <input name="app_secret" width="50" value="<?php echo $appSecret ?>">
        </label><br>

        <label>
            <span>Method</span>
            <select name="method">
                <option value="">Chose api method</option>
                <option value="verify_mobile"<?php echo (($method == 'verify_mobile')? ' selected=selected' : ' ') ?>>Verify Mobile</option>
                <option value="deposit_notice"<?php echo (($method == 'deposit_notice')? ' selected=selected' : ' ') ?>>Deposit Notice</option>
                <option value="withdrawal_confirm"<?php echo (($method == 'withdrawal_confirm')? ' selected=selected' : ' ') ?>>Withdrawal Confirm</option>
                <option value="transaction_confirm"<?php echo (($method == 'transaction_confirm')? ' selected=selected' : ' ') ?>>Transaction Confirm</option>
                <option value="order_confirm"<?php echo (($method == 'order_confirm')? ' selected=selected' : ' ') ?>>Order Confirm</option>
                <option value="received_order"<?php echo (($method == 'received_order')? ' selected=selected' : ' ') ?>>Received Order</option>
            </select>
            <?php if (isset($error['method']) && $error['method']) : ?>
            <span style="color: red"><?php echo $error['method'] ;?></span>
            <?php endif; ?>
        </label><br>

        <label>
            <span>Message</span>
            <input name="message" width="50" value="<?php echo $message ?>">
            <?php if (isset($error['message']) && $error['message']) : ?>
                <span style="color: red"><?php echo $error['message'] ;?></span>
            <?php endif; ?>
        </label><br>

        <label>
            <span>Short code</span>
            <input name="short_code" width="50" value="<?php echo $short_code ?>">
            <?php if (isset($error['short_code']) && $error['short_code']) : ?>
                <span style="color: red"><?php echo $error['short_code'] ;?></span>
            <?php endif; ?>
        </label><br>

        <label>
            <span>Phone</span>
            <input name="phone" width="50" value="<?php echo $phone ?>">
            <?php if (isset($error['phone']) && $error['phone']) : ?>
                <span style="color: red"><?php echo $error['phone'] ;?></span>
            <?php endif; ?>
        </label><br>

        <div style="width: 50%; text-align: center"><input name="submit" value="SEND" type="submit"></div>
    </div>
    <div style="width: 50%; float: right">
        <span>Time: <?php echo date('H:i:s d/m/Y'); ?></span><br>
        <span>URL: <?php echo $apiUrl .'/' .$method ?></span><br>
        <textarea rows="10" cols="80"><?php echo $response ?></textarea>
    </div>
</form>