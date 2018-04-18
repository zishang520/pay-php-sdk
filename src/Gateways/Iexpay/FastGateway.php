<?php

namespace Pay\Gateways\Iexpay;

use Pay\Gateways\Iexpay;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\Iexpay
 */
class FastGateway extends Iexpay
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        $options['productType'] = '40000503';
        return $this->getResult($options, 'quickGateWayPay/initPay');
    }

    public function url(array $options = [])
    {
        parent::apply();
        $options['productType'] = '40000503';
        return $this->buildUrl($options, 'quickGateWayPay/initPay');
    }
}
