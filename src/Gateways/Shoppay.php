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

use Exception;
use Pay\Contracts\Config;
use Pay\Contracts\GatewayInterface;
use Pay\Contracts\HttpService;
use Pay\Exceptions\GatewayException;
use Pay\Exceptions\InvalidArgumentException;
use Pay\Exceptions\UnexpectedValueException;

/**
 * 睿联抽象类
 * Class Shoppay
 * @package Pay\Gateways\Shoppay
 */
abstract class Shoppay extends GatewayInterface
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

    protected $payurl = 'http://www.laidian365.com/apisubmit';

    protected $queryurl = 'http://www.laidian365.com/apiorderquery';

    protected $transferurl = 'http://www.laidian365.com/apixiafa';

    /**
     * Shoppay constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->userConfig = new Config($config);
        if (is_null($this->userConfig->get('customerid'))) {
            throw new InvalidArgumentException('Missing Config -- [customerid]');
        }
        if (is_null($this->userConfig->get('merchantkey'))) {
            throw new InvalidArgumentException('Missing Config -- [merchantkey]');
        }
        if (!empty($config['cache_path'])) {
            HttpService::$cachePath = $config['cache_path'];
        }
        $this->config = [
            'customerid' => $this->userConfig->get('customerid'),
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
        $this->config = $options + ['version' => '1.0', 'paytype' => '', 'bankcode' => '', 'notifyurl' => $this->userConfig->get('notify_url'), 'returnurl' => $this->userConfig->get('return_url')] + $this->config;
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
     * @param string $sdorderno
     * @return array|bool
     * @throws GatewayException
     */
    public function find($sdorderno = '')
    {
        $options = ['sdorderno' => $sdorderno, 'reqtime' => date('YmdHis')];
        return $this->getResult($options, $this->queryurl, 3);
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
        return md5(sprintf('%s&%s', $this->getSignContent($data, 1), $this->userConfig->get('merchantkey'))) === $sign ? $data : false;
    }

    /**
     * 获取验证访问数据
     * @param string $url
     * @return array|bool
     * @throws GatewayException
     */
    protected function getResult($option, $url, $type = 0)
    {
        $this->config = $option + $this->config;
        $this->config['sign'] = $this->getSign($type);
        $response = $this->post($url, $this->config);
        try {
            list($code, $message) = explode(',', $response);
            var_dump($response);
            return ['code' => $code, 'msg' => $message];
        } catch (Exception $e) {
            var_dump($response);
            throw new UnexpectedValueException('Response Error.');
        }
    }

    /**
     * 获取数据签名
     * @return string
     */
    protected function getSign($type = 0)
    {
        return md5(sprintf('%s&%s', $this->getSignContent($this->config, $type), $this->userConfig->get('merchantkey')));
    }

    /**
     * 数据签名处理
     * @param array $toBeSigned
     * @param bool $verify
     * @return bool|string
     */
    protected function getSignContent(array $toBeSigned, $type = 0)
    {
        // 抛出异常
        set_error_handler(function ($line, $message) {
            throw new InvalidArgumentException('Parameter error,' . $message);
        });
        extract($toBeSigned);
        switch ($type) {
            // 异步通知签名
            case 1:
            //同步通知签名
            case 2:
                return "customerid={$customerid}&status={$status}&sdpayno={$sdpayno}&sdorderno={$sdorderno}&total_fee={$total_fee}&paytype={$paytype}";
                break;
            // 查询订单
            case 3:
                return "customerid={$customerid}&sdorderno={$sdorderno}&reqtime={$reqtime}";
                break;
            // 下发接口
            case 4:
                return "customerid={$customerid}&sdorderno={$sdorderno}&name={$name}&account={$account}&bank={$bank}&dizhi={$dizhi}&tel={$tel}";
                break;
            // 支付
            default:
                return "version={$version}&customerid={$customerid}&total_fee={$total_fee}&sdorderno={$sdorderno}&notifyurl={$notifyurl}&returnurl={$returnurl}";
                break;
        }
    }
}
