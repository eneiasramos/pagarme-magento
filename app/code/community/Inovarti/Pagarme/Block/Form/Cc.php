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
class Inovarti_Pagarme_Block_Form_Cc extends Mage_Payment_Block_Form_Cc
{
	const MIN_INSTALLMENT_VALUE = 5;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagarme/form/cc.phtml');
    }

    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        if (is_null($months)) {
            $months[0] =  $this->__('Month');
            for ($i=1; $i <= 12; $i++) {
                $months[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
            }
            $this->setData('cc_months', $months);
        }
        return $months;
    }

    public function getInstallmentsAvailables(){
        $plans = Mage::getModel('pagarme/config')->_getPlans();
        if(count($plans) > 0) {
            $allow_multiples = Mage::getStoreConfigFlag('payment/pagarme_subscriptions/allow_multiples');
            if(count($plans) > 1 && !$allow_multiples)
            {
                $this->_redirectToCart();

                return;
            }

            $_plan = reset($plans); // first plan
            $planId = key($plans);
            $_plan = Mage::getModel('pagarme/plans')->load($planId);
            $_planName = $_plan->getName();

        	$maxInstallments = $_plan->getInstallments();
        } else {
    	    $maxInstallments = (int)Mage::getStoreConfig('payment/pagarme_cc/max_installments');
        }

    	$minInstallmentValue = (float)Mage::getStoreConfig('payment/pagarme_cc/min_installment_value');
        $interestRate = (float)Mage::getStoreConfig('payment/pagarme_cc/interest_rate');
        $freeInstallments = (int)Mage::getStoreConfig('payment/pagarme_cc/free_installments');
    	if ($minInstallmentValue < self::MIN_INSTALLMENT_VALUE) {
    		$minInstallmentValue = self::MIN_INSTALLMENT_VALUE;
    	}

    	$quote = Mage::helper('pagarme')->_getQuote();
    	$total = Mage::helper('pagarme')->getBaseSubtotalWithDiscount () + Mage::helper ('pagarme')->getBaseShippingAmount ();

    	$n = floor($total / $minInstallmentValue);
    	if ($n > $maxInstallments) {
    		$n = $maxInstallments;
    	} elseif ($n < 1) {
    		$n = 1;
    	}

        $data = new Varien_Object();
        $data->setAmount(Mage::helper('pagarme')->formatAmount($total))
            ->setInterestRate($interestRate)
            ->setMaxInstallments($n)
            ->setFreeInstallments($freeInstallments) // optional
            ;

        $response = Mage::getModel('pagarme/api')->calculateInstallmentsAmount($data);
        $collection = $response->getInstallments();

        $installments = array();
        foreach ($collection as $item) {
            if ($item->getInstallment() == 1 && count($plans) == 0) {
                $label = $this->__('Pay in full - %s', $quote->getStore()->formatPrice($item->getInstallmentAmount()/100, false));
                $installments[$item->getInstallment()] = $label;
            } elseif (($item->getInstallment() != $maxInstallments && count($plans) == 0)
                      || ($item->getInstallment() == $maxInstallments && count($plans) > 0)) {
                $label = $this->__('%sx - %s', $item->getInstallment(), $quote->getStore()->formatPrice($item->getInstallmentAmount()/100, false)) . ' ';
                $label .= $item->getInstallment() > $freeInstallments ? $this->__('monthly interest rate (%s)', $interestRate.'%') : $this->__('interest-free');
                $label .= !empty($_planName) ? $this->__(' (Plan %s) ', $_planName) : null;
                $installments[$item->getInstallment()] = $label;
            }
        }
    	return $installments;
    }

    public function _redirectToCart()
    {
        Mage::getSingleton('core/session')->addError($this->__('Subscription of multiple plans are not allowed!'));

        $cartUrl = Mage::getUrl('checkout/cart/index');

        $response = Mage::app ()->getFrontController ()->getResponse ();
        $response->setRedirect ($cartUrl);
        $response->sendResponse ();
    }
}
