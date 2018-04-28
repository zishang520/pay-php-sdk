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
use soapClient;

/**
 * 支付宝抽象类
 * Class Cxfpay
 * @package Pay\Gateways\Cxfpay
 */
abstract class Cxfpay extends GatewayInterface
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
     * 支付宝网关地址
     * @var string
     */
    protected $gateway = 'http://114.215.172.196:7077/pay/CXFServlet/PaySmService?wsdl';

    /**
     * Cxfpay constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->userConfig = new Config($config);
        if (is_null($this->userConfig->get('merchantid'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantid]');
        }
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }
        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        $this->config = [
            'mid' => $this->userConfig->get('merchantid'),
            'sign' => $this->getSign(),
        ];
    }

    /**
     * 应用参数
     * @param array $options
     * @return mixed|void
     */
    public function apply(array $options = array())
    {
        $this->config['notifyUrl'] = $this->userConfig->get('notify_url');
    }

    /**
     * 支付宝订单退款操作
     * @param array|string $options 退款参数或退款商户订单号
     * @param null $refund_amount 退款金额
     * @return array|bool
     * @throws GatewayException
     */
    public function refund($options, $refund_amount = null)
    {
    }

    /**
     * 关闭支付宝进行中的订单
     * @param array|string $options
     * @return array|bool
     * @throws GatewayException
     */
    public function close($options)
    {
    }

    /**
     * 查询支付宝订单状态
     * @param string $out_trade_no
     * @return array|bool
     * @throws GatewayException
     */
    public function find($out_trade_no = '')
    {
        $options = ['orderNo' => $out_trade_no];
        return $this->getResult($options, 'queryPay');
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
        //amount=0.01&code=1&mid=105471000037&msg=%E6%94%AF%E4%BB%98%E6%88%90%E5%8A%9F&orderNo=1524926167&type=alipay_sm
        if (!isset($data['orderNo'])) {
            throw new InvalidArgumentException('orderNo is empty');
        }
        return $this->getResult(['orderNo' => $data['orderNo']], 'queryPay');
    }

    /**
     * 获取验证访问数据
     * @param array $options
     * @param string $method
     * @return array|bool
     * @throws GatewayException
     */
    protected function getResult($options, $method)
    {
        $options = array_merge($this->config, $options);
        $result = call_user_func([new soapClient($this->gateway), $method], json_encode($options));
        return json_decode($result, true);
    }

    /**
     * 获取数据签名
     * @return string
     */
    protected function getSign()
    {
        return openssl_encrypt($this->userConfig->get('merchantid'), 'DES-ECB', $this->userConfig->get('merchantkey'));
    }
}
