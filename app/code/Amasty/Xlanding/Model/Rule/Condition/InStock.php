<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Xlanding
 */


namespace Amasty\Xlanding\Model\Rule\Condition;

class InStock extends AbstractCondition
{
    protected $_inputType = 'select';
    protected $_string;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\Stdlib\StringUtils $string,
        array $data = []
    ) {
        $this->_string = $string;

        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
    }
    
    public function getAttributeElementHtml()
    {
        return __('In Stock');
    }

    public function getInputType()
    {
        return 'select';
    }

    public function getValueElementType()
    {
        return 'select';
    }

    protected function _getAttributeCode()
    {
        return 'in_stock';
    }

    protected function _prepareValueOptions()
    {
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');

        $selectOptions = [
            ['value' => 1, 'label' => 'Yes'],
            ['value' => 0, 'label' => 'No']
        ];

        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return $this;
    }

    public function collectValidatedAttributes($select)
    {
        $from = $select->getPart(\Zend_Db_Select::FROM);
        $where = $select->getPart(\Zend_Db_Select::WHERE);

        if ($this->getValue() && isset($from['stock_index'])) {
            //select already has stock condition
            return $select;
        }

        //if stock condition exists and value = 0 ( out of stock products )
        if (!$this->getValue() && isset($from['stock_index']) && $from['stock_index']['joinCondition']) {
            $from['stock_index']['joinCondition'] = str_replace(
                '`stock_status` = 1',
                '`stock_status` = 0',
                $from['stock_index']['joinCondition']
            );
            $select->setPart(\Zend_Db_Select::FROM, $from);
            return $select;
        }

        //situation where select doesn't have stock index table - deprecated - remove in future
        if (isset($from['stock_index']) ) {
            unset($from['stock_index']);
            foreach ($where as $idx => $condition) {
                if ($this->_string->strpos($condition, 'stock_index') !== false) {
                    unset($where[$idx]);
                }
            }

            $select->setPart(\Zend_Db_Select::FROM, $from);
            $select->setPart(\Zend_Db_Select::WHERE, $where);
        }

        $dAlias = 'tad_' . $this->_getAlias();
        $sAlias = 'tas_' . $this->_getAlias();
        $select->joinLeft(
            [$dAlias => $this->_productResource->getTable('cataloginventory_stock_status')],
            'e.entity_id = '. $dAlias .'.product_id'
            . $this->_productResource->getConnection()->quoteInto(
                ' AND '. $dAlias .'.website_id = ?',
                '0'
            ),
            []
        );
        $select->joinLeft(
            [$sAlias => $this->_productResource->getTable('cataloginventory_stock_status')],
            'e.entity_id = '. $sAlias .'.product_id'
            . $this->_productResource->getConnection()->quoteInto(
                ' AND '. $sAlias .'.website_id = ?',
                $this->getStoreManager()->getWebsite()->getId()
            ),
            []
        );

        $this->prepareCondition($dAlias, $sAlias);
        $select->distinct(true);
        $select->where($this->_condition);
    }

    /**
     * @param string $dAlias
     * @param string $sAlias
     * @return $this
     */
    protected function prepareCondition($dAlias, $sAlias)
    {
        $tmp = 'tmp_stock_alias';
        $value     = $this->getValue();
        $operator  = $this->getOperatorForValidate();

        $condition = $this->getOperatorCondition($tmp, $operator, $value);

        $limit = 1;
        $eavColumn = "IFNULL(`$sAlias`.`stock_status`, `$dAlias`.`stock_status`)";
        $condition = str_replace('`' . $tmp . '`', $eavColumn, $condition, $limit);

        $this->_condition = $condition;
        return $this;
    }

}
