<?php

// +----------------------------------------------------------------------
// | pay-php-sdk
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/pay-php-sdk
// +----------------------------------------------------------------------

include '../init.php';

// 加载配置参数
$config = require __DIR__ . '/config.php';

$options = array(
    "orderNo" => time(), //商户订单号
    "subject" => "TEST", //订单标题
    "body" => "TEST", //订单描述
    "amount" => "0.01", //订单金额
    "type" => "alipay_sm", //支付种类:union_sm:银联扫码;QQwap:QQ钱包Wap;QQwallet:QQ钱包扫码
    "buyerName" => "测试人", //买家姓名
    "buyerId" => "e88f9384-a6d6-4c63-97b8", //买家在商城的唯一编号
    "payRemark" => "测试付款", //付款摘要
    "extNetIp" => "112.193.144.191", //用户设备外网
);
// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $options = $pay->driver('cxfpay')->gateway('fast')->apply($options);
    var_dump($options);
} catch (Exception $e) {
    echo "创建订单失败，" . $e->getMessage();
}
