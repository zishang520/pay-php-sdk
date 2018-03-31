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
$options = [
    'pay_orderid' => time(), // 订单号
    'pay_amount' => '0.01', // 订单金额，**单位：分**
    'pay_productname' => '测试商品', //商品名称
    'pay_productdesc' => '订单描述', // 订单描述
    'pay_productnum' => 1, //商品数量， 选填
    'pay_producturl' => 'http://localhost/notify.php', // 商品地址
];

// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $result = $pay->driver('ruilian')->gateway('AlipayScan')->apply($options);
    var_dump($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
