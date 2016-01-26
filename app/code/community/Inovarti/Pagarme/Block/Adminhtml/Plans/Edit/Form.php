<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Plans_Edit_Form
extends Mage_Adminhtml_Block_Widget_Form
{

protected function _prepareForm()
{
	$form = new Varien_Data_Form(array(
	    "id" => "edit_form",
	    "action" => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
	    "method" => "post",
	    "enctype" =>"multipart/form-data",
	));

	$form->setUseContainer(true);
	$this->setForm($form);

	return parent::_prepareForm();
}

}

