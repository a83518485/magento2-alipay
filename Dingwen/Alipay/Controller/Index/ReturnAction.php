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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Dingwen\Alipay\Controller\AbstractAction;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Helper\Data;
use Magento\Customer\Model\Group;
use Magento\Quote\Api\CartManagementInterface;
use Dingwen\Alipay\Model\OrderCancellationService;
use Dingwen\Alipay\Model\Adapter\AlipayAdapterFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Dingwen\Alipay\Model\AlipayGatewayConfig;
use Omnipay\Alipay\Responses\AopTradeQueryResponse;

/**
 * Class PlaceOrder
 */
class ReturnAction extends AbstractAction implements HttpPostActionInterface,HttpGetActionInterface
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
        AlipayGatewayConfig $config
    ) {
        parent::__construct($context, $checkoutSession, $config);
        $this->customerSession = $customerSession;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->checkoutHelper = $checkoutHelper;
        $this->cartManagement = $cartManagement;
        $this->orderCancellationService = $orderCancellationService;
        $this->alipayAdapterFactory = $alipayAdapterFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $quote = $this->checkoutSession->getQuote();
        $gateway = $this->alipayAdapterFactory->create()->getGateway();
        try {

            $params = array_merge($_POST, $_GET);
            $request = $gateway->query();
            $request->setBizContent([
                'trade_no'=>$params['trade_no'] ?? null,
            ]);
            /**@var $response AopTradeQueryResponse */
            $response = $request->send();

            $quote->getPayment()->setAdditionalInformation(array_merge(
                $quote->getPayment()->getAdditionalInformation(),
                $response->getAlipayResponse()
            ));

            if ($response->isPaid()) {
                $this->_orderPlace($quote);
            }

            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            return $resultRedirect->setPath('checkout/onepage/success', ['_secure' => true]);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addExceptionMessage(
                $e,
                'The order #' . $quote->getReservedOrderId() . ' cannot be processed.'
            );
        }

        return $resultRedirect->setPath('checkout/cart', ['_secure' => true]);
    }

    protected function _orderPlace(Quote $quote)
    {

        if ($this->getCheckoutMethod($quote) === Onepage::METHOD_GUEST) {
            $this->prepareGuestQuote($quote);
        }

        $this->disabledQuoteAddressValidation($quote);

        $quote->collectTotals();
        try {
            $this->cartManagement->placeOrder($quote->getId());
        } catch (\Exception $e) {
            $this->orderCancellationService->execute($quote->getReservedOrderId());
            throw $e;
        }
    }

    /**
     * Get checkout method
     *
     * @param Quote $quote
     * @return string
     */
    private function getCheckoutMethod(Quote $quote)
    {
        if ($this->customerSession->isLoggedIn()) {
            return Onepage::METHOD_CUSTOMER;
        }
        if (!$quote->getCheckoutMethod()) {
            if ($this->checkoutHelper->isAllowedGuestCheckout($quote)) {
                $quote->setCheckoutMethod(Onepage::METHOD_GUEST);
            } else {
                $quote->setCheckoutMethod(Onepage::METHOD_REGISTER);
            }
        }

        return $quote->getCheckoutMethod();
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param Quote $quote
     * @return void
     */
    private function prepareGuestQuote(Quote $quote)
    {
        $quote->setCustomerId(null)
              ->setCustomerEmail($quote->getBillingAddress()->getEmail())
              ->setCustomerIsGuest(true)
              ->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);
    }

    /**
     * Make sure addresses will be saved without validation errors
     *
     * @param Quote $quote
     * @return void
     */
    private function disabledQuoteAddressValidation(Quote $quote)
    {
        $billingAddress = $quote->getBillingAddress();
        $billingAddress->setShouldIgnoreValidation(true);

        if (!$quote->getIsVirtual()) {
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setShouldIgnoreValidation(true);
            if (!$billingAddress->getEmail()) {
                $billingAddress->setSameAsBilling(1);
            }
        }
    }
}
