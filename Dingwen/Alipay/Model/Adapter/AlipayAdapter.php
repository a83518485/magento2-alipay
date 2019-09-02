<?php
/**
 * Created by PhpStorm.
 * User: dingwen
 * Date: 2019/08/11
 * Time: 0:37
 */

namespace Dingwen\Alipay\Model\Adapter;


use Omnipay\Omnipay;
use Omnipay\Alipay\AopPageGateway;
use Dingwen\Alipay\Model\AlipayGatewayConfig;
use Magento\Framework\UrlInterface;


class AlipayAdapter
{
    /**
     * @var AlipayGatewayConfig
     */
    protected $_config;

    /**
     * @var UrlInterface
     */
    protected $_url;

    protected $_gateway = null;

    const SIGN_TYPE = "sign_type";
    const APP_ID = "app_id";
    const PRIVATE_KEY = "merchant_private_key";
    const ALIPAY_PUBLIC_KEY = "alipay_public_key";
    const SANDBOX = "sandbox";
    const RETURN_URL = "";
    const NOTIFY_URL = "";


    public function __construct(AlipayGatewayConfig $config,UrlInterface $url)
    {
        $this->_config = $config;
        $this->_url = $url;
        $this->initCredentials();

    }

    protected function initCredentials()
    {
        /**
         * @var AopPageGateway $gateway
         */
        $gateway = Omnipay::create('Alipay_AopPage');
        $gateway->setSignType($this->_config->getValue(self::SIGN_TYPE)); //RSA/RSA2
        $gateway->setAppId($this->_config->getValue(self::APP_ID));
        $gateway->setPrivateKey($this->_config->getValue(self::PRIVATE_KEY));
        $gateway->setAlipayPublicKey($this->_config->getValue(self::ALIPAY_PUBLIC_KEY));
        $gateway->setReturnUrl($this->_url->getUrl('alipay/index/return'));
        //$gateway->setNotifyUrl('');

        if ($this->_config->getValue(self::SANDBOX)) {
            $gateway->sandbox();
        } else {
            $gateway->production();
        }

        $this->_gateway = $gateway;
    }

    /**
     * @return AopPageGateway
     */
    public function getGateway()
    {
        return $this->_gateway;
    }


}