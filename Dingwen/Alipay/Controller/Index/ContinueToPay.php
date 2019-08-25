<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dingwen\Alipay\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Onepage checkout success controller class
 */
class ContinueToPay extends \Magento\Checkout\Controller\Onepage implements HttpGetActionInterface
{
    /**
     * Order success action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
