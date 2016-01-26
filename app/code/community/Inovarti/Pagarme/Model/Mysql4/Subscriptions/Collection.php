<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Mysql4_Subscriptions_Collection
extends Mage_Core_Model_Mysql4_Collection_Abstract
{

public function _construct()
{
    $this->_init("pagarme/subscriptions");
}

}

