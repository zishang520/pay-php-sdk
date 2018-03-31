<?php

namespace Pay\Gateways\Ruilian;

use Pay\Gateways\Ruilian;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\Ruilian
 */
class JdpayGateway extends Ruilian
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        $options['pay_bankcode'] = '910';
        return $this->getResult($options, $this->payurl);
    }
}
