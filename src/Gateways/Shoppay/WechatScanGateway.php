<?php

namespace Pay\Gateways\Shoppay;

use Pay\Gateways\Shoppay;

/**
 * 支付宝App支付网关
 * Class AppGateway
 * @package Pay\Gateways\Shoppay
 */
class WechatScanGateway extends Shoppay
{

    /**
     * 应用并返回参数
     * @param array $options
     * @return string
     */
    public function apply(array $options = [])
    {
        parent::apply();
        $options['paytype'] = 'weixin';
        return $this->getResult($options, $this->payurl);
    }
}
