<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Factory class for \Magento\Paypal\Model\Api\AbstractApi
 */
namespace Dingwen\Alipay\Model\Adapter;

use Dingwen\Alipay\Model\Adapter\AlipayAdapter;

class AlipayAdapterFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;


    protected $_config;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     * @return AlipayAdapter
     */
    public function create()
    {
        return $this->_objectManager->create(AlipayAdapter::class);
    }
}
