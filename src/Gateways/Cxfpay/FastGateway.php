<?php

namespace Pay\Gateways\Cxfpay;

use Pay\Gateways\Cxfpay;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\KjAlipay
 */
class FastGateway extends Cxfpay
{
    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        return $this->getResult($options, 'pay');
    }
}
