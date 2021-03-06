<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Plans_Edit_Tabs
extends Mage_Adminhtml_Block_Widget_Tabs
{

public function __construct()
{
	parent::__construct();

	$this->setId("plans_tabs");
	$this->setDestElementId("edit_form");
	$this->setTitle(Mage::helper("pagarme")->__("Item Information"));
}

protected function _beforeToHtml()
{
	$this->addTab("form_section", array(
	"label" => Mage::helper("pagarme")->__("Item Information"),
	"title" => Mage::helper("pagarme")->__("Item Information"),
	"content" => $this->getLayout()->createBlock("pagarme/adminhtml_plans_edit_tab_form")->toHtml(),
	));

	return parent::_beforeToHtml();
}

}

