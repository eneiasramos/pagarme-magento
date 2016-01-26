<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */
	
class Inovarti_Pagarme_Block_Adminhtml_Plans_Edit
extends Mage_Adminhtml_Block_Widget_Form_Container
{

public function __construct()
{
	parent::__construct();

	$this->_objectId = "id";
	$this->_blockGroup = "pagarme";
	$this->_controller = "adminhtml_plans";
	//$this->_updateButton("save", "label", Mage::helper("pagarme")->__("Save Item"));
	//$this->_updateButton("delete", "label", Mage::helper("pagarme")->__("Delete Item"));

	$this->_addButton("saveandcontinue", array(
		"label"     => Mage::helper("pagarme")->__("Save and Continue Edit"),
		"onclick"   => "saveAndContinueEdit()",
		"class"     => "save",
	), -100);

	$this->_formScripts[] = "
		function saveAndContinueEdit(){
			editForm.submit($('edit_form').action+'back/edit/');
		}
	";
}

public function getHeaderText()
{
	if(Mage::registry("plans_data") && Mage::registry("plans_data")->getId())
    {
	    return Mage::helper("pagarme")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("plans_data")->getId()));
	}
	else
    {
        return Mage::helper("pagarme")->__("Add Item");
	}
}

}

