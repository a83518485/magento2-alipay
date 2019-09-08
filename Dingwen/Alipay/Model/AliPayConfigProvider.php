<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dingwen\Alipay\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\UrlInterface;
use Omnipay\Alipay\Requests\AopTradePagePayRequest;
use Omnipay\Alipay\Responses\AopTradePagePayResponse;
use \Dingwen\Alipay\Model\Adapter\AlipayAdapterFactory;

class AliPayConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string[]
     */
    protected $methodCode = PCPAY::PAYMENT_METHOD_ALIPAY_CODE;

    /**
     * @var PCPAY
     */
    protected $method;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;


    protected $gateway;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote = false;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;



    /**
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     *
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        AlipayAdapterFactory $alipayAdapterFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->urlBuilder      = $urlBuilder;
        $this->method          = $paymentHelper->getMethodInstance($this->methodCode);
        $this->gateway         = $alipayAdapterFactory->create()->getGateway();
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Return checkout quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if ( !$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }


//    public function getRedirectUrl()
//    {
//        $request = $this->gateway->purchase();
//        $quote   = $this->getQuote();
//        $quote->reserveOrderId();
//        $this->quoteRepository->save($quote);
//        $request->setBizContent([
//            'out_trade_no' => $quote->getReservedOrderId(),
//            'total_amount' => round($quote->getBaseGrandTotal(), 2),
//            'subject' => 'Magento2 测试订单',
//            'product_code' => 'FAST_INSTANT_TRADE_PAY',
//        ]);
//
//        /**
//         * @var AopTradePagePayResponse $response
//         */
//        $response = $request->send();
//
//        $redirectUrl = $response->getRedirectUrl();
//
//        return $redirectUrl;
//    }

    /**
     * Retrieve continue to pay page URL
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getContinueToPayUrl()
    {
        return $this->urlBuilder->getUrl('alipay/index/continuetopay/');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->method->isAvailable() ? [
            'continueToPayPageUrl' => $this->getContinueToPayUrl(),
            'checkoutRedirectUrl' => $this->urlBuilder->getUrl('alipay/index/start'),
        ] : [];
    }

}
