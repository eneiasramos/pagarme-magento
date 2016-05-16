<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 *
 * UPDATED:
 *
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */
require_once(Mage::getModuleDir(null,'Inovarti_Pagarme').DS.'libs'.DS.'pagarme-php'.DS.'pagarme.php');

class Inovarti_Pagarme_Model_Boleto extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'pagarme_boleto';

    protected $_formBlockType = 'pagarme/form_boleto';
    protected $_infoBlockType = 'pagarme/info_boleto';

	protected $_isGateway                   = true;
	protected $_canUseForMultishipping 		= false;
	protected $_isInitializeNeeded      	= true;
	protected $_canManageRecurringProfiles  = false;

	public function initialize($paymentAction, $stateObject)
    {
    	$payment = $this->getInfoInstance();
        $order = $payment->getOrder();

        $plans = $this->_getPlans($payment);
        if(!empty($plans)) {
            $this->_processPlans($payment, $plans);
            return $this;
        }

        $this->_place($payment, $order->getBaseTotalDue());
        return $this;
    }

    public function _place(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
        $customer = Mage::helper('pagarme')->getCustomerInfoFromOrder($payment->getOrder());
        $data = new Varien_Object();
		$data->setPaymentMethod(Inovarti_Pagarme_Model_Api::PAYMENT_METHOD_BOLETO)
			->setAmount(Mage::helper('pagarme')->formatAmount($amount))
            ->setBoletoExpirationDate($this->_generateExpirationDate())
			->setCustomer($customer)
			->setPostbackUrl(Mage::getUrl('pagarme/transaction_boleto/postback'));

		$pagarme = Mage::getModel('pagarme/api');

		$transaction = $pagarme->charge($data);
		if ($transaction->getErrors()) {
			$messages = array();
			foreach ($transaction->getErrors() as $error) {
				$messages[] = $error->getMessage() . '.';
			}
			Mage::log(implode("\n", $messages), null, 'pagarme.log');
			Mage::throwException(implode("\n", $messages));
		}

		// pagar.me info
		$payment->setPagarmeTransactionId($transaction->getId())
			->setPagarmeBoletoUrl($transaction->getBoletoUrl()) // PS: Pagar.me in test mode always returns NULL
			->setPagarmeBoletoBarcode($transaction->getBoletoBarcode())
			->setPagarmeBoletoExpirationDate($transaction->getBoletoExpirationDate());

		return $this;
    }

    protected function _generateExpirationDate()
    {
        $days = $this->getConfigData('days_to_expire');
        $result = Mage::getModel('core/date')->date('Y-m-d H:i:s', strtotime("+ $days days"));
        return $result;
    }

    protected function _getPlans($payment)
    {
        return Mage::getModel('pagarme/config')->_getPlans($payment);
    }

    protected function _processPlans($payment, $plans)
    {
        $allow_multiples = Mage::getStoreConfigFlag('payment/pagarme_subscriptions/allow_multiples');
        if(count($plans) > 1 && !$allow_multiples)
        {
            Mage::throwException(Mage::helper('pagarme')->__('Subscription of multiple plans are not allowed!'));
        }

        $api_mode = Mage::getStoreConfig('payment/pagarme_settings/mode');
        $api_key = Mage::getStoreConfig('payment/pagarme_settings/apikey_' . $api_mode);
        Pagarme::setApiKey($api_key);

        $customer_email = $payment->getOrder()->getCustomerEmail();
        $result = null;

        foreach($plans as $id => $qty)
        {
            $_plan = Mage::getModel('pagarme/plans')->load($id);

            $subscription = new PagarMe_Subscription(array(
                'plan' => PagarMe_Plan::findById($_plan->getRemoteId()),
                'payment_method' => 'boleto',
                'customer' => array(
                    'email' => $customer_email
                ),
                'postback_url' => Mage::getUrl('pagarme/transaction_subscription/postback', array(
                    'id' => $payment->getOrder()->getId()
                ))
            ));

            $subscription->create();
            $result = $subscription->current_transaction;

            $transaction = Mage::getModel('pagarme/subscriptions')
                ->setPlanId($_plan->getId())
                ->setRemotePlanId($_plan->getRemoteId())
                ->setRemoteId($subscription->getId())
                ->setTransactionId($result->getId())
                ->setOrderId($payment->getOrder()->getId())
                ->setPaymentMethod($result->getPaymentMethod())
                ->setAmount(intval($result->getAmount()) / 100)
                ->setCost($result->getCost())
                ->setRemoteIP($result->getIp())
			    ->setBoletoUrl($result->getBoletoUrl()) // PS: Pagar.me in test mode always returns NULL
                ->setBoletoBarcode($result->getBoletoBarcode())
                ->setBoletoExpirationDate($result->getBoletoExpirationDate())
                ->setStatus($result->getStatus())
                ->setCreatedAt($result->getDateCreated())
                ->setUpdatedAt($result->getDateUpdated())
                ->save();
        }

		// pagar.me info
		$payment->setPagarmeSubscriptionId($subscription->getId())
			->setPagarmeTransactionId($result->getId())
			->setPagarmeBoletoUrl($result->getBoletoUrl()) // PS: Pagar.me in test mode always returns NULL
			->setPagarmeBoletoBarcode($result->getBoletoBarcode())
			->setPagarmeBoletoExpirationDate($result->getBoletoExpirationDate());

        $payment->setTransactionAdditionalInfo(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS,array('status' => $result->getStatus()));
    }
}

