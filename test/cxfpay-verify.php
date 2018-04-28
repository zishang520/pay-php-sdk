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

// 实例支付对象
$pay = new \Pay\Pay($config);

parse_str('amount=0.01&code=1&mid=105471000037&msg=%E6%94%AF%E4%BB%98%E6%88%90%E5%8A%9F&orderNo=1524926167&type=alipay_sm', $_REQUEST);

try {
    $options = $pay->driver('cxfpay')->gateway('fast')->verify($_REQUEST);
    var_dump($options);
} catch (Exception $e) {
    echo "创建订单失败，" . $e->getMessage();
}
