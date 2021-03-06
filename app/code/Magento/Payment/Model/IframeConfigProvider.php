<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Helper\Data as PaymentHelper;
use Psr\Log\LoggerInterface;

abstract class IframeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * Payment method code
     *
     * @var string
     */
    protected $methodCode;

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod
     */
    protected $method;

    /**
     * @param Repository $assetRepo
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param LoggerInterface $logger
     * @param PaymentHelper $paymentHelper
     */
    public function __construct(
        Repository $assetRepo,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        LoggerInterface $logger,
        PaymentHelper $paymentHelper
    ) {
        $this->assetRepo = $assetRepo;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
        $this->paymentHelper = $paymentHelper;
        $this->method = $this->paymentHelper->getMethodInstance($this->methodCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'iframe' => [
                    'dateDelim' => [$this->methodCode => $this->getDateDelim()],
                    'cardFieldsMap' => [$this->methodCode => $this->getCardFieldsMap()],
                    'source' =>  [$this->methodCode => $this->getViewFileUrl('blank.html')],
                    'controllerName' => [$this->methodCode => $this->getController()],
                    'cgiUrl' => [$this->methodCode => $this->getCgiUrl()],
                    'placeOrderUrl' => [$this->methodCode => $this->getPlaceOrderUrl()],
                    'saveOrderUrl' => [$this->methodCode => $this->getSaveOrderUrl()],
                ],
            ],
        ];
    }

    /**
     * Get delimiter for date
     *
     * @return string
     */
    protected function getDateDelim()
    {
        $result = '';
        if ($this->method->isAvailable()) {
            $configData = $this->getMethodConfigData('date_delim');
            if ($configData !== null) {
                $result = $configData;
            }
        }

        return  $result;
    }

    /**
     * Get map of cc_code, cc_num, cc_expdate for gateway
     * Returns json formatted string
     *
     * @return string
     */
    protected function getCardFieldsMap()
    {
        $result = [];
        if ($this->method->isAvailable()) {
            $configData = $this->getMethodConfigData('ccfields');
            $keys = ['cccvv', 'ccexpdate', 'ccnum'];
            $result = array_combine($keys, explode(',', $configData));
        }

        return $result;
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string[]
     */
    protected function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $params = array_merge(['_secure' => $this->request->isSecure()], $params);
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    protected function getController()
    {
        return $this->request->getControllerName();
    }

    /**
     * Retrieve place order url on front
     *
     * @return string
     */
    protected function getPlaceOrderUrl()
    {
        return $this->urlBuilder->getUrl(
            $this->getMethodConfigData('place_order_url'),
            [
                '_secure' => $this->request->isSecure()
            ]
        );
    }

    /**
     * Retrieve save order url on front
     *
     * @return string
     */
    protected function getSaveOrderUrl()
    {
        return $this->urlBuilder->getUrl('checkout/onepage/saveOrder', ['_secure' => $this->request->isSecure()]);
    }

    /**
     * Retrieve gateway url
     *
     * @return string
     */
    protected function getCgiUrl()
    {
        return (bool)$this->getMethodConfigData('sandbox_flag')
            ? $this->getMethodConfigData('cgi_url_test_mode')
            : $this->getMethodConfigData('cgi_url');
    }

    /**
     * Retrieve config data value by field name
     *
     * @param string $fieldName
     * @return mixed
     */
    protected function getMethodConfigData($fieldName)
    {
        return $this->method->getConfigInterface()->getConfigValue($fieldName);
    }
}
