<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace DingWen\Alipay\Model;

/**
 * Class alipay
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 *
 * @api
 * @since 100.0.2
 */
class AliPay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_ALIPAY_CODE = 'alipay';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_ALIPAY_CODE;

    

}
