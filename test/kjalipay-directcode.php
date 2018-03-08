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

// 支付参数
$payOrder = [
    'merchant_order_no' => '41234123', // 商户订单号
    'trade_amount' => '1', // 支付金额
    'goods_name' => '蛤蟆', // 支付订单描述
    'goods_desc' => '免费蛤蟆', // 支付订单描述
];

// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $options = $pay->driver('kjalipay')->gateway('directcode')->apply($payOrder);
    var_dump($options);
} catch (Exception $e) {
    echo "创建订单失败，" . $e->getMessage();
}
