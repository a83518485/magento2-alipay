<?php
/**
 * Created by PhpStorm.
 * User: dingwen
 * Date: 2019/08/11
 * Time: 16:29
 */

namespace Dingwen\Alipay\Block;

use \Magento\Checkout\Block\Onepage\Success;
use \Dingwen\Alipay\Model\Adapter\AlipayAdapterFactory;
use Omnipay\Alipay\Requests\AopTradePagePayRequest;
use Omnipay\Alipay\Responses\AopTradePagePayResponse;

class ContinueToPay extends Success
{

    protected $gateway;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        AlipayAdapterFactory $alipayAdapterFactory,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->gateway = $alipayAdapterFactory->create()->getGateway();
    }


    protected function prepareBlockData() {
        parent::prepareBlockData();
        $order = $this->_checkoutSession->getLastRealOrder();
        /**@var AopTradePagePayRequest $request */
        $request = $this->gateway->purchase();
        $request->setBizContent([
            'out_trade_no' => $order->getIncrementId(),
            'total_amount' => $order->getGrandTotal(),
            'subject'      => 'Magento2 测试订单',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ]);
        /**
         * @var AopTradePagePayResponse $response
         */
        $response = $request->send();

        $redirectUrl = $response->getRedirectUrl();

        $this->setData("redirect_url", $redirectUrl);


    }

}