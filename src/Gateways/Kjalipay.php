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
// | 项目设计及部分源码参考于 yansongda/pay，在此特别感谢！
// +----------------------------------------------------------------------

namespace Pay\Gateways;

use Pay\Contracts\Config;
use Pay\Contracts\GatewayInterface;
use Pay\Contracts\HttpService;
use Pay\Exceptions\GatewayException;
use Pay\Exceptions\InvalidArgumentException;
use Pay\Exceptions\UnexpectedValueException;

/**
 * 支付宝抽象类
 * Class Kjalipay
 * @package Pay\Gateways\Kjalipay
 */
abstract class Kjalipay extends GatewayInterface
{

    /**
     * 支付宝全局参数
     * @var array
     */
    protected $config;

    /**
     * 用户定义配置
     * @var Config
     */
    protected $userConfig;

    /**
     * 主地址
     * @var string
     */
    protected $gateway = 'http://api.kj-pay.com';

    /**
     * Kjalipay constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->userConfig = new Config($config);

        if (is_null($this->userConfig->get('api_url'))) {
            throw new InvalidArgumentException('Missing Config -- [api_url]');
        }
        if (is_null($this->userConfig->get('merchantid'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantid]');
        }
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }

        $this->gateway = $this->userConfig->get('api_url');

        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        $this->config = [
            'merchant_no' => $this->userConfig->get('merchantid'),
            'sign_type' => '1',
            'sign' => '',
        ];
    }

    /**
     * 应用参数
     * @param array $options
     * @return mixed|void
     */
    public function apply(array $options = [])
    {
        $this->config['app_no'] = $this->userConfig->get('app_no');
        $this->config['notify_url'] = $this->userConfig->get('notify_url');
        $this->config['start_time'] = date('YmdHis');
    }

    /**
     * [refund 退款]
     * @Author    ZiShang520@gmail.com
     * @DateTime  2018-03-08T13:09:40+0800
     * @copyright (c) ZiShang520 All Rights Reserved
     * @param     [type] $options [description]
     * @param     [type] $refund_reason [description]
     * @return    [type] [description]
     */
    public function refund($options, $refund_reason = null)
    {
        if (!is_array($options)) {
            $options = ['trade_no' => $options, 'refund_reason' => $refund_reason];
        }
        return $this->getResult($options, '/alipay/trade_refund');
    }

    /**
     * 关闭订单
     * @param $options
     * @return mixed
     */
    public function close($options)
    {}

    /**
     * 查询支付宝订单状态
     * @param string $trade_no
     * @return array|bool
     * @throws GatewayException
     */
    public function find($trade_no = '')
    {
        $options = ['trade_no' => $trade_no];
        return $this->getResult($options, '/alipay/query_pay');
    }

    /**
     * 验证支付宝支付宝通知
     * @param array $data 通知数据
     * @param null $sign 数据签名
     * @param bool $sync
     * @return array|bool
     */
    public function verify($data, $sign = null, $sync = false)
    {
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }
        $sign = is_null($sign) ? $data['sign'] : $sign;
        return md5(sprintf('%s&key=%s', $this->getSignContent($data), $this->userConfig->get('merchantkey'))) === $sign ? $data : false;
    }

    /**
     * 获取验证访问数据
     * @param string $method
     * @return array|bool
     * @throws GatewayException
     */
    protected function getResult($option, $method)
    {
        $this->config = array_merge($this->config, $option);
        $this->config['sign'] = $this->getSign();
        $data = json_decode($this->post($this->gateway . $method, $this->config), true);
        if ($data) {
            return $data;
        }
        throw new UnexpectedValueException('Response Error.');
    }

    /**
     * 获取数据签名
     * @return string
     */
    protected function getSign()
    {
        return md5(sprintf('%s&key=%s', $this->getSignContent($this->config), $this->userConfig->get('merchantkey')));
    }

    /**
     * 数据签名处理
     * @param array $toBeSigned
     * @param bool $verify
     * @return bool|string
     */
    protected function getSignContent(array $toBeSigned, $verify = false)
    {
        ksort($toBeSigned);
        unset($toBeSigned['sign']);
        return urldecode(http_build_query(array_filter($toBeSigned, function ($v) {
            return $v !== '';
        })));
    }
}
