<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Source_Checkout_Paymentmethod
{

public function toOptionArray ()
{
    $options = array(
        /*
        array(
            'value' => 'boleto',
            'label' => __('Boleto'),
        ),
        */
        array(
            'value' => 'credit_card',
            'label' => __('Credit Card'),
        ),
    );

    return $options;
}

public function toArray ()
{
    $result = array ();
    
    foreach ($this->toOptionArray () as $child)
    {
        $result[$child['value']] = Mage::helper('pagarme')->__($child['label']);
    }

    return $result;
}

}

