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
 * 睿联抽象类
 * Class Ruilian
 * @package Pay\Gateways\Ruilian
 */
abstract class Ruilian extends GatewayInterface
{

    /**
     * 睿联全局参数
     * @var array
     */
    protected $config;

    /**
     * 用户定义配置
     * @var Config
     */
    protected $userConfig;

    protected $payurl = 'http://www.silverspay.com/Pay_Index.html';

    protected $queryurl = 'http://www.silverspay.com/Pay_Trade_query.html';

    protected $transferurl = 'http://www.silverspay.com/Payservice_Index.html';

    /**
     * Ruilian constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->userConfig = new Config($config);
        if (is_null($this->userConfig->get('pay_memberid'))) {
            throw new InvalidArgumentException('Missing Config -- [pay_memberid]');
        }
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }
        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        $this->config = [
            'pay_memberid' => $this->userConfig->get('pay_memberid'),
            'pay_md5sign' => '',
        ];
    }

    /**
     * 应用参数
     * @param array $options
     * @return mixed|void
     */
    public function apply(array $options = [])
    {
        $this->config = $options + ['pay_bankcode' => '', 'pay_notifyurl' => $this->userConfig->get('notify_url'), 'pay_callbackurl' => $this->userConfig->get('return_url'), 'pay_applydate' => date('Y-m-d H:i:s')] + $this->config;
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
    {}

    /**
     * 关闭订单
     * @param $options
     * @return mixed
     */
    public function close($options)
    {}

    /**
     * 查询睿联订单状态
     * @param string $pay_orderid
     * @return array|bool
     * @throws GatewayException
     */
    public function find($pay_orderid = '')
    {
        $options = ['pay_orderid' => $pay_orderid];
        return $this->getResult($options, $this->queryurl);
    }

    /**
     * 验证睿联通知
     * @param array $data 通知数据
     * @param null $sign 数据签名
     * @return array|bool
     */
    public function verify($data, $sign = null, $sync = false)
    {
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }
        $sign = is_null($sign) ? $data['sign'] : $sign;
        return strtoupper(md5(sprintf('%s&key=%s', $this->getSignContent($data), $this->userConfig->get('merchantkey')))) === $sign ? $data : false;
    }

    /**
     * 获取验证访问数据
     * @param string $url
     * @return array|bool
     * @throws GatewayException
     */
    protected function getResult($option, $url)
    {
        $this->config = $option + $this->config;
        $this->config['pay_md5sign'] = $this->getSign();
        $data = json_decode($this->post($url, $this->config), true);
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
        return strtoupper(md5(sprintf('%s&key=%s', $this->getSignContent($this->config), $this->userConfig->get('merchantkey'))));
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
        unset($toBeSigned['pay_md5sign']);
        return urldecode(http_build_query(array_filter($toBeSigned, function ($v) {
            return $v !== '';
        })));
    }
}
