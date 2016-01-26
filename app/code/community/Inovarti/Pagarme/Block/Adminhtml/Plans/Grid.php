<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Plans_Grid
extends Mage_Adminhtml_Block_Widget_Grid
{

public function __construct()
{
	parent::__construct();

	$this->setId("pagarmePlansGrid");
	$this->setDefaultSort("id");
	$this->setDefaultDir("DESC");
	$this->setSaveParametersInSession(true);
}

protected function _prepareCollection()
{
	$collection = Mage::getModel("pagarme/plans")->getCollection();
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
	$this->addColumn("name", array(
	    "header" => Mage::helper("pagarme")->__("Name"),
	    "align" => "right",
	    "index" => "name",
	));
	$this->addColumn("amount", array(
	    "header" => Mage::helper("pagarme")->__("Amount"),
	    "align" => "right",
        "type" => "number",
	    "index" => "amount",
	));
	$this->addColumn("days", array(
	    "header" => Mage::helper("pagarme")->__("Days"),
	    "align" => "right",
        "type" => "number",
	    "index" => "days",
	));
/*
	$this->addColumn("trial_days", array(
	    "header" => Mage::helper("pagarme")->__("Trial Days"),
	    "align" => "right",
        "type" => "number",
	    "index" => "trial_days",
	));
*/
	$this->addColumn("payment_methods", array(
	    "header" => Mage::helper("pagarme")->__("Payment Methods"),
	    "align" => "right",
	    "index" => "payment_methods",
        "type"  => "options",
        "options" => Mage::getModel('pagarme/source_checkout_paymentmethod')->toArray()
	));
	$this->addColumn("charges", array(
	    "header" => Mage::helper("pagarme")->__("Charges"),
	    "align" => "right",
        "type" => "number",
	    "index" => "charges",
	));
	$this->addColumn("installments", array(
	    "header" => Mage::helper("pagarme")->__("Installments"),
	    "align" => "right",
        "type" => "number",
	    "index" => "installments",
	));
	$this->addColumn("is_active", array(
	    "header" => Mage::helper("pagarme")->__("Is Active"),
	    "align" => "right",
	    "index" => "is_active",
        "type" => "options",
        "options" => Mage::getModel('adminhtml/system_config_source_yesno')->toArray(),
	));

    //$this->addRssList('pagarme/adminhtml_rss_rss/plans', Mage::helper('pagarme')->__('RSS'));
	//$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV')); 
	//$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

	return parent::_prepareColumns();
}

public function getRowUrl($row)
{
    return $this->getUrl("*/*/edit", array("id" => $row->getId()));
}

protected function _prepareMassaction_()
{
	$this->setMassactionIdField('id');
	$this->getMassactionBlock()->setFormFieldName('ids');
	$this->getMassactionBlock()->setUseSelectAll(true);
	$this->getMassactionBlock()->addItem('remove_plans', array(
			 'label'=> Mage::helper('pagarme')->__('Remove Plans'),
			 'url'  => $this->getUrl('*/adminhtml_plans/massRemove'),
			 'confirm' => Mage::helper('pagarme')->__('Are you sure?')
	));

	return $this;
}

}

