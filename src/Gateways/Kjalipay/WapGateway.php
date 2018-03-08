<?php

namespace Pay\Gateways\Kjalipay;

use Pay\Gateways\Kjalipay;

/**
 * 手机WAP支付网关
 * Class WapGateway
 * @package Pay\Gateways\Kjalipay
 */
class WapGateway extends Kjalipay
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
        return $this->getResult($options, '/alipay/wap_pay');
    }
}
