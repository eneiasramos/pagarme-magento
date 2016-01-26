<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Plans
extends Mage_Adminhtml_Block_Widget_Grid_Container
{

public function __construct()
{
    $this->_controller = "adminhtml_plans";
    $this->_blockGroup = "pagarme";
    $this->_headerText = Mage::helper("pagarme")->__("Plans Manager");
    //$this->_addButtonLabel = Mage::helper("pagarme")->__("Add New Item");

    parent::__construct();
}

}

