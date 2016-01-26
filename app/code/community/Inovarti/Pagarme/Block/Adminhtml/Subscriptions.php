<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Subscriptions
extends Mage_Adminhtml_Block_Widget_Grid_Container
{

public function __construct()
{
    parent::__construct();

    $this->_controller = "adminhtml_subscriptions";
    $this->_blockGroup = "pagarme";
    $this->_headerText = Mage::helper("pagarme")->__("Subscriptions Manager");
    //$this->_addButtonLabel = Mage::helper("pagarme")->__("Add New Item");
    $this->_removeButton('add');
}

}

