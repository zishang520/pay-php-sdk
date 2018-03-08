<?php

namespace Pay\Gateways\Kjalipay;

use Pay\Gateways\Kjalipay;

/**
 * 支付宝扫码支付
 * Class ScanGateway
 * @package Pay\Gateways\Kjalipay
 */
class ScanGateway extends Kjalipay
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return array|bool
     * @throws \Pay\Exceptions\GatewayException
     */
    public function apply(array $options = [])
    {
        parent::apply();
        return $this->getResult($options, '/alipay/scan_pay');
    }
}
