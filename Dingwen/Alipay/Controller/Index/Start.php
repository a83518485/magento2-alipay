<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dingwen\Alipay\Controller\Index;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Dingwen\Alipay\Controller\AbstractAction;
use Magento\Checkout\Helper\Data;
use Magento\Quote\Api\CartManagementInterface;
use Dingwen\Alipay\Model\OrderCancellationService;
use Dingwen\Alipay\Model\Adapter\AlipayAdapterFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Dingwen\Alipay\Model\AlipayGatewayConfig;
use Omnipay\Alipay\Responses\AopTradePagePayResponse;

/**
 * Class PlaceOrder
 */
class Start extends AbstractAction implements HttpPostActionInterface,HttpGetActionInterface
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * Logger for exception details
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var Data
     */
    private $checkoutHelper;

    /**
     * @var OrderCancellationService
     */
    private $orderCancellationService;

    /**
     * @var AlipayAdapterFactory
     */
    private $alipayAdapterFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        LoggerInterface $logger = null,
        \Magento\Customer\Model\Session $customerSession,
        Data $checkoutHelper,
        CartManagementInterface $cartManagement,
        orderCancellationService $orderCancellationService,
        AlipayAdapterFactory $alipayAdapterFactory,
        AlipayGatewayConfig $config,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct($context, $checkoutSession, $config);
        $this->customerSession = $customerSession;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->checkoutHelper = $checkoutHelper;
        $this->cartManagement = $cartManagement;
        $this->orderCancellationService = $orderCancellationService;
        $this->alipayAdapterFactory = $alipayAdapterFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        $alipayAdapter = $this->alipayAdapterFactory->create();
        $request = $alipayAdapter->getGateway()->purchase();
        $order   = $this->checkoutSession->getLastRealOrder();

        $request->setBizContent([
            'out_trade_no' => $order->getIncrementId(),
            'total_amount' => round($order->getBaseGrandTotal(), 2),
            'subject' => 'Magento2 测试订单',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
        ]);

        /**
         * @var AopTradePagePayResponse $response
         */
        $response = $request->send();

        $redirectUrl = $response->getRedirectUrl();

        $this->getResponse()->setRedirect($redirectUrl);

        return;
    }
}
