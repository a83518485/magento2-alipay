<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dingwen\Alipay\Controller\Index;

use Magento\Braintree\Gateway\Config\PayPal\Config;
use Magento\Braintree\Model\Paypal\Helper;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use \Dingwen\Alipay\Controller\AbstractAction;

/**
 * Class PlaceOrder
 */
class ReturnAction extends AbstractAction implements HttpPostActionInterface
{
    /**
     * @var Helper\OrderPlace
     */
    private $orderPlace;

    /**
     * Logger for exception details
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config $config
     * @param Session $checkoutSession
     * @param Helper\OrderPlace $orderPlace
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        Config $config,
        Session $checkoutSession,
        Helper\OrderPlace $orderPlace,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context, $config, $checkoutSession);
        $this->orderPlace = $orderPlace;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
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

        try {
            $this->validateQuote($quote);

            $this->orderPlace->execute($quote);

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

    protected function _orderPlace($quote)
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
}
