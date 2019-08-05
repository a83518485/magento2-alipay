<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dingwen\Alipay\Model\Config\Source;


/**
 * @api
 * @since 100.0.2
 */
class SignType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => "RSA2", 'label' => __('RSA2')], ['value' => "RSA", 'label' => __('RSA')]];
    }

}
