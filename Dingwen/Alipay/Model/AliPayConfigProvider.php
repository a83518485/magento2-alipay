<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dingwen\Alipay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface;

class AliPayConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = PCPay::PAYMENT_METHOD_CHECKMO_CODE;

    /**
     * @var PCPay
     */
    protected $method;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->method = $paymentHelper->getMethodInstance($this->methodCode);
    }

    /**
     * Retrieve continue to pay page URL
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getContinueToPayUrl()
    {
        return $this->urlBuilder->getUrl('checkout/onepage/continuetopay/');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'payment' => [
                'continueToPayPageUrl' => [
                    $this->getContinueToPayUrl()
                ],
            ],
        ] : [];
    }

}
