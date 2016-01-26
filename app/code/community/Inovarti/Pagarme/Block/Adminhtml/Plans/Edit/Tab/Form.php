<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Plans_Edit_Tab_Form
extends Mage_Adminhtml_Block_Widget_Form
{
protected function _prepareForm()
{
	$form = new Varien_Data_Form();
	$this->setForm($form);
	$fieldset = $form->addFieldset("pagarme_form", array("legend"=>Mage::helper("pagarme")->__("Item Information")));

    $payment_methods = Mage::getModel ('pagarme/source_checkout_paymentmethod')->toOptionArray();

    $fieldset->addField("remote_id", "label", array(
        "label" => Mage::helper("pagarme")->__("Remote ID"),
        "name" => "remote_id",
        "class" => "",
        "required" => false
    ));
    $fieldset->addField("name", "text", array(
        "label" => Mage::helper("pagarme")->__("Name"),
        "name" => "name",
        "class" => "required-entry",
        "required" => true
    ));
    $fieldset->addField("amount", "text", array(
        "label" => Mage::helper("pagarme")->__("Amount"),
        "name" => "amount",
        "class" => "required-entry",
        "required" => true
    ));
    $fieldset->addField("days", "text", array(
        "label" => Mage::helper("pagarme")->__("Days"),
        "name" => "days",
        "class" => "required-entry",
        "required" => true
    ));
/*
    $fieldset->addField("trial_days", "text", array(
        "label" => Mage::helper("pagarme")->__("Trial Days"),
        "name" => "trial_days",
        "class" => "required-entry",
        "required" => true
    ));
*/
    $fieldset->addField("payment_methods", "multiselect", array(
        "label" => Mage::helper("pagarme")->__("Payment Methods"),
        "name" => "payment_methods",
        "class" => "required-entry",
        "required" => true,
        "values" => $payment_methods
    ));
    $fieldset->addField("charges", "text", array(
        "label" => Mage::helper("pagarme")->__("Charges"),
        "name" => "charges",
        "class" => "",
        "required" => false
    ));
    $fieldset->addField("installments", "text", array(
        "label" => Mage::helper("pagarme")->__("Installments"),
        "name" => "installments",
        "class" => "required-entry",
        "required" => true
    ));
    $fieldset->addField("is_active", "select", array(
        "label" => Mage::helper("pagarme")->__("Is Active"),
        "name" => "is_active",
        "class" => "required-entry",
        "required" => true,
        "options" => Mage::getModel('adminhtml/system_config_source_yesno')->toArray (),
    ));

	if (Mage::getSingleton("adminhtml/session")->getPlansData())
	{
		$form->setValues(Mage::getSingleton("adminhtml/session")->getPlansData());
		Mage::getSingleton("adminhtml/session")->setPlansData(null);
	} 
	elseif(Mage::registry("plans_data"))
    {
	    $form->setValues(Mage::registry("plans_data")->getData());
	}

	return parent::_prepareForm();
}

}

