<?php

namespace Pay\Gateways\Kjalipay;

use Pay\Gateways\Kjalipay;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\Kjalipay
 */
class DirectcodeGateway extends Kjalipay
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        $options['return_url'] = isset($options['return_url']) ? $options['return_url'] : $this->userConfig->get('return_url', '');
        $options['qrcode_width'] = isset($options['qrcode_width']) ? $options['qrcode_width'] : 230;
        return $this->getResult($options, '/alipay/direct_code');
    }
}
