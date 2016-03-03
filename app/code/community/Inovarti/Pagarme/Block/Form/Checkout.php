<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Form_Checkout
extends Mage_Payment_Block_Form
{

protected function _construct ()
{
    parent::_construct ();

    $this->setTemplate ('pagarme/form/checkout.phtml');
}

public function _getEncryptionKey ()
{
    return $this->_getHelper ()->getEncryptionKey();
}

public function _getPostbackUrl ()
{
    return $this->getUrl ('pagarme/checkout/transaction');
}

public function _getInfoData ($field)
{
    return $this->escapeHtml (Mage::getStoreConfig ("payment/pagarme_checkout/{$field}"));
}

public function _getQuote ()
{
    return Mage::getModel ('checkout/session')->getQuote ();
}

public function _getAmount ()
{
    return $this->_getHelper ()->formatAmount ($this->_getQuote ()->getBaseGrandTotal ());
}

public function _getAddressData ($field)
{
    return $this->_getQuote ()->getBillingAddress ()->getData ($field);
}

public function _getStreet ($id)
{
    $street = explode (PHP_EOL, $this->_getAddressData ('street'));

    return $street [$id - 1];
}

public function _getState ()
{
    $region_id = $this->_getQuote ()->getBillingAddress ()->getRegionId ();

    $collection = Mage::getModel ('directory/region')->getResourceCollection ()->addCountryFilter ('BR');
    $collection->getSelect ()->where ("main_table.region_id = '{$region_id}'");

    $result = $collection->getFirstItem ()->getCode ();

    return $result;
}

public function _getDocumentNumber ()
{
    return $this->_getHelper ()->_numberOnly ($this->_getQuote ()->getCustomer ()->getTaxvat ());
}

public function _getTelephone ($start, $length)
{
    return $this->_getHelper ()->_iSubstr ($this->_getHelper ()->_numberOnly ($this->_getAddressData ('telephone')), $start, $length);
}

public function _getCardBrands ($cctypes)
{
    $types = explode (',', $this->_getInfoData ($cctypes));
    
    $model = Mage::getModel ('pagarme/source_cctype');
    
    $result = null;
    foreach ($types as $value)
    {
        $result [] = $model->getBrandByType ($value);
    }
    
    return implode (',', $result);
}

public function _getHelper ()
{
    return $this->helper ('pagarme');
}

protected function _toHtml()
{
    Mage::dispatchEvent('payment_form_block_to_html_before', array(
        'block'     => $this
    ));
    return parent::_toHtml();
}

}

