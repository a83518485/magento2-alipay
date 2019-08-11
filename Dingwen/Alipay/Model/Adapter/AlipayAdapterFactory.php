<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Factory class for \Magento\Paypal\Model\Api\AbstractApi
 */
namespace Dingwen\Alipay\Model\Adapter;

use Magento\Payment\Gateway\ConfigInterface;
use Dingwen\Alipay\Model\Adapter\AlipayAdapter;

class AlipayAdapterFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @var ConfigInterface
     */
    protected $_config;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ConfigInterface $config
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, ConfigInterface $config)
    {
        $this->_objectManager = $objectManager;
        $this->_config = $config;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return \Magento\Paypal\Model\Api\AbstractApi
     */
    public function create()
    {
        return $this->_objectManager->create(AlipayAdapter::class);
    }
}
