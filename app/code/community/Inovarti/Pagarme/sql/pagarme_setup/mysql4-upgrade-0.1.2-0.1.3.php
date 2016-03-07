<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

$installer = new Mage_Sales_Model_Resource_Setup();
$installer->startSetup();

// Order Payment
$entity = 'order_payment';
$attributes = array(
	'pagarme_subscription_id' => array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER)
);

foreach ($attributes as $attribute => $options)
{
	$installer->addAttribute($entity, $attribute, $options);
}

$installer->endSetup();

