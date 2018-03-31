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
    'sdorderno' => time(), // 订单号
    'bankcode' => 'ICBC',
    'total_fee' => '0.01', // 订单金额，**单位：分**
    'remark' => '测试商品', //商品名称
];

// 实例支付对象
$pay = new \Pay\Pay($config);

try {
    $result = $pay->driver('shoppay')->gateway('UnionOnlinepay')->apply($options);
    var_dump($result);
} catch (Exception $e) {
    echo $e->getMessage();
}
