<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Config
{

public function _getPlans()
{
    $active = Mage::getStoreConfigFlag('payment/pagarme_subscriptions/active');
    if(!$active) return;

    $productAttributeCode = Inovarti_Pagarme_Helper_Data::SUBSCRIPTION_PLAN_ATTRIBUTE_CODE;
    $result = null;

    $quote = Mage::getSingleton('checkout/session')->getQuote();

    foreach($quote->getAllItems() as $item)
    {
        $value = $item->getProduct()->getData($productAttributeCode);
        if(!empty($value)) $result [$value] += 1;
    }

    return $result;
}

}

