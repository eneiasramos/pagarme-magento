<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 *
 * UPDATED:
 *
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Source_Status
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_PROCESSING,
                'label' => Mage::helper('pagarme')->__('Processing')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_AUTHORIZED,
                'label' => Mage::helper('pagarme')->__('Authorized')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_PAID,
                'label' => Mage::helper('pagarme')->__('Paid')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_WAITING_PAYMENT,
                'label' => Mage::helper('pagarme')->__('Waiting Payment')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_REFUSED,
                'label' => Mage::helper('pagarme')->__('Refused')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_REFUNDED,
                'label' => Mage::helper('pagarme')->__('Refunded')
            ),
        );
    }

    public function getOptionLabel($value)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
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
