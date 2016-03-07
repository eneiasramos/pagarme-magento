<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Subscriptions_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{

public function __construct()
{
	parent::__construct();

	$this->setId("pagarmeSubscriptionsGrid");
	$this->setDefaultSort("id");
	$this->setDefaultDir("DESC");
	$this->setSaveParametersInSession(true);
}

protected function _prepareCollection()
{
    $sfo = Mage::getSingleton('core/resource')->getTableName('sales/order');

	$collection = Mage::getModel("pagarme/subscriptions")->getCollection();
    $collection->getSelect()->join(
        array('sfo' => $sfo),
        'main_table.order_id = sfo.entity_id',
        array('increment_id', 'order_status' => 'sfo.status')
    );
	$this->setCollection($collection);

	return parent::_prepareCollection();
}

protected function _prepareColumns()
{
	$this->addColumn("id", array(
	    "header" => Mage::helper("pagarme")->__("ID"),
	    "align" => "right",
	    "width" => "50px",
        "type" => "number",
	    "index" => "id",
	));
	$this->addColumn("remote_id", array(
	    "header" => Mage::helper("pagarme")->__("Remote ID"),
	    "align" => "right",
	    "width" => "50px",
        "type" => "number",
	    "index" => "remote_id",
	));
	$this->addColumn("transaction_id", array(
	    "header" => Mage::helper("pagarme")->__("Transaction ID"),
	    "align" => "right",
	    "width" => "50px",
        "type" => "number",
	    "index" => "transaction_id",
	));
	$this->addColumn("increment_id", array(
	    "header" => Mage::helper("pagarme")->__("Order Increment ID"),
	    "align" => "right",
	    "width" => "50px",
        "type" => "number",
	    "index" => "increment_id",
	));
	$this->addColumn("payment_method", array(
	    "header" => Mage::helper("pagarme")->__("Payment Method"),
	    "align" => "right",
	    "index" => "payment_method",
        "type"  => "options",
        "options" => Mage::getModel('pagarme/source_subscription_paymentmethod')->toArray()
	));
	$this->addColumn("amount", array(
	    "header" => Mage::helper("pagarme")->__("Amount"),
	    "align" => "right",
        "type" => "number",
	    "index" => "amount",
	));
	$this->addColumn("installments", array(
	    "header" => Mage::helper("pagarme")->__("Installments"),
	    "align" => "right",
        "type" => "number",
	    "index" => "installments",
	));
	$this->addColumn("cost", array(
	    "header" => Mage::helper("pagarme")->__("Cost"),
	    "align" => "right",
	    "index" => "cost",
	));
	$this->addColumn("remote_id", array(
	    "header" => Mage::helper("pagarme")->__("Remote ID"),
	    "align" => "right",
        "type" => "number",
	    "index" => "remote_id",
	));
	$this->addColumn("authorization_code", array(
	    "header" => Mage::helper("pagarme")->__("Authorization Code"),
	    "align" => "right",
        "type" => "number",
	    "index" => "authorization_code",
	));
	$this->addColumn("tid", array(
	    "header" => Mage::helper("pagarme")->__("TID"),
	    "align" => "right",
        "type" => "number",
	    "index" => "tid",
	));
	$this->addColumn("created_at", array(
	    "header" => Mage::helper("pagarme")->__("Created At"),
	    "align" => "right",
	    "index" => "created_at",
        "type" => "datetime",
        "format" => "dd/MM/yyyy",
	));
    /*
	$this->addColumn("updated_at", array(
	    "header" => Mage::helper("pagarme")->__("Updated At"),
	    "align" => "right",
	    "index" => "updated_at",
	));
    */
	$this->addColumn("order_status", array(
	    "header" => Mage::helper("pagarme")->__("Order Status"),
	    "align" => "right",
	    "index" => "order_status",
        "type"  => "options",
        "options" => Mage::getSingleton('sales/order_config')->getStatuses()
	));

    //$this->addRssList('pagarme/adminhtml_rss_rss/subscriptions', Mage::helper('pagarme')->__('RSS'));
	//$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
	//$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

	return parent::_prepareColumns();
}

public function getRowUrl($row)
{
    return $this->getUrl("adminhtml/sales_order/view", array("order_id" => $row->getOrderId()));
}

protected function _prepareMassaction_()
{
	$this->setMassactionIdField('id');
	$this->getMassactionBlock()->setFormFieldName('ids');
	$this->getMassactionBlock()->setUseSelectAll(true);
	$this->getMassactionBlock()->addItem('remove_subscriptions', array(
			 'label'=> Mage::helper('pagarme')->__('Remove Subscriptions'),
			 'url'  => $this->getUrl('*/adminhtml_subscriptions/massRemove'),
			 'confirm' => Mage::helper('pagarme')->__('Are you sure?')
	));

	return $this;
}

}

