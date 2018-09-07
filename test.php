<?php
require 'vendor/autoload.php';

use Leslie\Sms\Sms;

//api key可在后台查看 短信->触发发送下面查看
$sms = new Sms(array('api_key' => 'xxxxxxxxxxxxxxxxxxxxx', 'use_ssl' => false));

//send 单发接口，签名需在后台报备
$res = $sms->send('xxxxxxxxxxxxx', '验证码：19272【铁壳测试】');
//sendbatch群发接口
//$res = $sms->send_batch($mobile_list = array(), $message = array(), $time = '');
if ($res) {
    if (isset($res['error']) && $res['error'] == 0) {
        echo 'success';
    } else {
        echo 'failed,code:' . $res['error'] . ',msg:' . $res['msg'];
    }
} else {
    var_dump($sms->last_error());
}
exit;
//deposit 余额查询
$res = $sms->get_deposit();
if ($res) {
    if (isset($res['error']) && $res['error'] == 0) {
        echo 'desposit:' . $res['deposit'];
    } else {
        echo 'failed,code:' . $res['error'] . ',msg:' . $res['msg'];
    }
} else {
    var_dump($sms->last_error());
}
exit;