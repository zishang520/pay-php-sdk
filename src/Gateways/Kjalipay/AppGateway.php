<?php

namespace Pay\Gateways\Kjalipay;

use Pay\Gateways\Kjalipay;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\KjAlipay
 */
class AppGateway extends Kjalipay
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        return $this->getResult($options, '/alipay/app_pay');
    }
}
