<?php
/*
 * @package     Inovarti_Pagarme
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author      Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Transaction_CreditcardController
extends Mage_Core_Controller_Front_Action
{

public function postbackAction()
{
	$pagarme = Mage::getModel('pagarme/api');
	$request = $this->getRequest();

	if ($request->isPost() && $pagarme->validateFingerprint($request->getPost('id'), $request->getPost('fingerprint')))
	{
		$orderId = Mage::helper('pagarme')->getOrderIdByTransactionId($request->getPost('id'));
		$order = Mage::getModel('sales/order')->load($orderId);

		$currentStatus = $request->getPost('current_status');
        switch($currentStatus)
        {
        case Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_PAID:
        {
            $order->setState(Mage_Sales_Model_Order::STATE_NEW)->save();

		    if (!$order->canInvoice())
            {
			    Mage::throwException($this->__('The order does not allow creating an invoice.'));
		    }

		    $invoice = Mage::getModel('sales/service_order', $order)
			    ->prepareInvoice()
			    ->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE)
			    ->register()
			    ->pay();

		    $invoice->setEmailSent(true);
		    $invoice->getOrder()->setIsInProcess(true);

		    $transactionSave = Mage::getModel('core/resource_transaction')
			    ->addObject($invoice)
			    ->addObject($invoice->getOrder())
			    ->save();

		    $invoice->sendEmail();

            $order->addStatusHistoryComment($this->__('Approved by Pagarme via Creditcard postback.'))->save();

		    $this->getResponse()->setBody('ok');

		    return;
        }
        case Inovarti_Pagarme_Model_Api::TRANSACTION_STATUS_REFUSED:
        {
            $order->setState(Mage_Sales_Model_Order::STATE_NEW)->save();

            if (!$order->canCancel())
            {
			    Mage::throwException($this->__('Order does not allow to be canceled.'));
            }

            $order->cancel()->save();
            $order->addStatusHistoryComment($this->__('Canceled by Pagarme via Creditcard postback.'))->save();

            $this->getResponse()->setBody('ok');

            return;
        }
        } // switch
	}

	$this->_forward('404');
}

}

