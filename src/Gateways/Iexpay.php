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
 * Class Iexpay
 * @package Pay\Gateways\Iexpay
 */
abstract class Iexpay extends GatewayInterface
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
    protected $gateway = 'https://gateway.iexbuy.com/';

    /**
     * Iexpay constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->userConfig = new Config($config);

        if (is_null($this->userConfig->get('payKey'))) {
            throw new InvalidArgumentException('Missing Config -- [payKey]');
        }
        if (is_null($this->userConfig->get('paySecret'))) {
            throw new InvalidArgumentException('Missing Config -- [paySecret]');
        }
        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        $this->config = [
            'payKey' => $this->userConfig->get('payKey'),
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
        $this->config['returnUrl'] = $this->userConfig->get('return_url');
        $this->config['notifyUrl'] = $this->userConfig->get('notify_url');
        $this->config['orderTime'] = date('YmdHis');
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
        $options = ['outTradeNo' => $trade_no];
        return $this->getResult($options, 'query/singleOrder');
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
        $sign = is_null($sign) ? $data['sign'] : $sign;
        return strtoupper(md5(sprintf('%s&paySecret=%s', $this->getSignContent($data), $this->userConfig->get('paySecret')))) === strtoupper($sign) ? $data : false;
    }

    protected function buildUrl($option, $method)
    {
        $this->config = array_merge($this->config, $option);
        $this->config['sign'] = $this->getSign();
        return sprintf('%s%s?%s', $this->gateway, $method, http_build_query($this->config));
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
        $response = $this->post($this->gateway . $method, $this->config, ['headers' => [
            'User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1',
        ]]);
        if ($msg = json_decode($response, true)) {
            return $msg;
        }
        if (stripos($response, 'http') === 0) {
            return ['status' => 200, 'msg' => 'ok', 'url' => $response];
        }
        throw new UnexpectedValueException('Response Error.');
    }

    /**
     * 获取数据签名
     * @return string
     */
    protected function getSign()
    {
        return strtoupper(md5(sprintf('%s&paySecret=%s', $this->getSignContent($this->config), $this->userConfig->get('paySecret'))));
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
