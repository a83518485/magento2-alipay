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
use Magento\Payment\Gateway\ConfigInterface;

class AlipayAdapter
{
    /**
     * @var ConfigInterface
     */
    protected $_config;

    protected $_gateway = null;

    const SIGN_TYPE = "sign_type";
    const APP_ID = "app_id";
    const PRIVATE_KEY = "merchant_private_key";
    const ALIPAY_PUBLIC_KEY = "alipay_public_key";
    const SANDBOX = "sandbox";
    const RETURN_URL = "";
    const NOTIFY_URL = "";


    public function __construct(ConfigInterface $config)
    {
        $this->_config = $config;
        echo "<pre>";
        var_dump($this->_config);
        echo "</pre>";
        exit;
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
        $gateway->setReturnUrl($this->_config->getValue(self::RETURN_URL));
        $gateway->setNotifyUrl($this->_config->getValue(self::NOTIFY_URL));

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