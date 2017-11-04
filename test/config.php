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

return [
    // 微信支付参数
    'wechat' => [
        'debug'      => true, // 沙箱模式
        'app_id'     => 'wxe335431b79068046', // 应用ID
        'mch_id'     => '1300513101', // 微信支付商户号
        'mch_key'    => 'AGNq9Z6I9xQ7usWT2xPXc76pS9HUvcoq', // 微信支付密钥
        'ssl_cer'    => __DIR__ . '/cert/1300513101_cert.pem', // 微信证书 cert 文件
        'ssl_key'    => __DIR__ . '/cert/1300513101_key.pem', // 微信证书 key 文件
        'notify_url' => 'http://localhost/wxpay-notify.php', // 支付通知URL
        'return_url' => 'http://localhost/wxpay-notify.php', // WEB支付成功后返回地址
    ],
    // 支付宝支付参数
    'alipay' => [
        'debug'       => true, // 沙箱模式
        'app_id'      => '', // 应用ID
        'public_key'  => '', // 支付宝公钥(1行填写)
        'private_key' => '', // 支付宝私钥(1行填写)
        'notify_url'  => 'http://localhost/wxpay-notify.php', // 支付通知URL
    ]
];