<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Conditions
 */


namespace Amasty\Conditions\Plugin\Payment\Helper;

class Data
{
    /**
     * @var \Magento\Payment\Model\Config
     */
    private $paymentConfig;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var array
     */
    private $functionParams;

    public function __construct(
        \Magento\Payment\Model\Config $paymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\Payment\Helper\Data $subject
     * @param bool $sorted
     * @param bool $asLabelValue
     * @param bool $withGroups
     * @param null $store
     *
     * @return array
     */
    public function beforeGetPaymentMethodList(
        \Magento\Payment\Helper\Data $subject,
        $sorted = true,
        $asLabelValue = false,
        $withGroups = false,
        $store = null
    ) {
        $this->functionParams = [$sorted, $asLabelValue, $withGroups, $store];

        return [$sorted, $asLabelValue, $withGroups, $store];
    }

    /**
     * @param \Magento\Payment\Helper\Data $subject
     * @param $result
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetPaymentMethodList(
        \Magento\Payment\Helper\Data $subject,
        $result
    ) {
        if (version_compare($this->productMetadata->getVersion(), '2.1.10', '<')) {
            return $result;
        }

        list($sorted, $asLabelValue, $withGroups, $store) = $this->functionParams;
        $methods = [];
        $groups = [];
        $groupRelations = [];

        foreach ($subject->getPaymentMethods() as $code => $data) {
            if (isset($data['title'])) {
                $methods[$code] = $data['title'];
            } else {
                $methods[$code] = $subject->getMethodInstance($code)->getConfigData('title', $store);
            }
            if ($asLabelValue && $withGroups && isset($data['group'])) {
                $groupRelations[$code] = $data['group'];
            }
        }
        if ($asLabelValue && $withGroups) {
            $groups = $this->paymentConfig->getGroups();
            foreach ($groups as $code => $title) {
                $methods[$code] = $title;
            }
        }
        if ($sorted) {
            asort($methods);
        }
        if ($asLabelValue) {
            $labelValues = [];
            foreach ($methods as $code => $title) {
                $labelValues[$code] = [];
            }
            foreach ($methods as $code => $title) {
                if (isset($groups[$code])) {
                    $labelValues[$code]['label'] = $title;
                } elseif (isset($groupRelations[$code])) {
                    unset($labelValues[$code]);
                    $labelValues[$groupRelations[$code]]['value'][$code] = ['value' => $code, 'label' => $title];
                } else {
                    $labelValues[$code] = ['value' => $code, 'label' => $title];
                }
            }

            return $labelValues;
        }

        return $methods;
    }
}