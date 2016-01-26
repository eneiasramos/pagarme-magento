<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Product_Attribute_Plan
extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

public function getAllOptions()
{
    $this->_options = array(
        array(
            'value' => '',
            'label' => Mage::helper('catalog')->__('-- Please Select --'),
        ),
    );

    $collection = Mage::getModel('pagarme/plans')->getCollection();
    foreach ($collection as $child)
    {
        $amount = number_format($child->getAmount(), 2, ',', '.');

        $this->_options[] = array(
            'value' => $child->getId (),
            'label' => sprintf("%s (%sx %s)", $child->getName (), $child->getInstallments(), $amount),
        );
    }

    return $this->_options;
}

public function getOptionArray()
{
    $_options = array();
    foreach ($this->getAllOptions() as $option)
    {
        $_options[$option['value']] = $option['label'];
    }

    return $_options;
}

public function getOptionText($value)
{
    $options = $this->getAllOptions();
    foreach ($options as $option)
    {
        if ($option['value'] == $value)
        {
            return $option['label'];
        }
    }

    return false;
}

public function getFlatColums()
{
    $attributeCode = $this->getAttribute()->getAttributeCode();
    $column = array(
        'unsigned'  => true,
        'default'   => null,
        'extra'     => null
    );

    if (Mage::helper('core')->useDbCompatibleMode())
    {
        $column['type']     = 'tinyint';
        $column['is_null']  = true;
    }
    else
    {
        $column['type']     = Varien_Db_Ddl_Table::TYPE_SMALLINT;
        $column['nullable'] = true;
        $column['comment']  = 'Pagarme - Subscription Plan ' . $attributeCode . ' column';
    }

    return array($attributeCode => $column);
}

public function getFlatIndexes()
{
    $indexes = array();

    $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
    $indexes[$index] = array(
        'type'      => 'index',
        'fields'    => array($this->getAttribute()->getAttributeCode())
    );

    return $indexes;
}

public function getFlatUpdateSelect($store)
{
    return Mage::getResourceSingleton('eav/entity_attribute')
        ->getFlatUpdateSelect($this->getAttribute(), $store);
}

}

