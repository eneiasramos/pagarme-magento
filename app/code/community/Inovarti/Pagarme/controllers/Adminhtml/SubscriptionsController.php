<?php
/*
 * @copyright   Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

require_once(Mage::getModuleDir(null,'Inovarti_Pagarme').DS.'libs'.DS.'pagarme-php'.DS.'pagarme.php');

class Inovarti_Pagarme_Adminhtml_SubscriptionsController
extends Mage_Adminhtml_Controller_Action
{

protected function _initAction()
{
	$this->loadLayout()->_setActiveMenu("pagarme/subscriptions")->_addBreadcrumb(Mage::helper("adminhtml")->__("Subscriptions  Manager"),Mage::helper("adminhtml")->__("Subscriptions Manager"));

	return $this;
}

public function indexAction() 
{
    $this->_title($this->__("Pagarme"));
    $this->_title($this->__("Manager Subscriptions"));

	$this->_initAction();
	$this->renderLayout();
}

public function editAction()
{			    
    $this->_title($this->__("Pagarme"));
	$this->_title($this->__("Subscriptions"));
    $this->_title($this->__("Edit Item"));
	
	$id = $this->getRequest()->getParam("id");
	$model = Mage::getModel("pagarme/subscriptions")->load($id);
	if ($model->getId())
    {
		Mage::register("subscriptions_data", $model);
		$this->loadLayout();
		$this->_setActiveMenu("pagarme/subscriptions");
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Subscriptions Manager"), Mage::helper("adminhtml")->__("Subscriptions Manager"));
		$this->_addBreadcrumb(Mage::helper("adminhtml")->__("Subscriptions Description"), Mage::helper("adminhtml")->__("Subscriptions Description"));
		$this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock("pagarme/adminhtml_subscriptions_edit"))->_addLeft($this->getLayout()->createBlock("pagarme/adminhtml_subscriptions_edit_tabs"));
		$this->renderLayout();
	} 
	else
    {
		Mage::getSingleton("adminhtml/session")->addError(Mage::helper("pagarme")->__("Item does not exist."));
		$this->_redirect("*/*/");
	}
}

public function newAction()
{
    $this->_title($this->__("Pagarme"));
    $this->_title($this->__("Subscriptions"));
    $this->_title($this->__("New Item"));

    $id   = $this->getRequest()->getParam("id");
    $model  = Mage::getModel("pagarme/subscriptions")->load($id);

    $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
    if (!empty($data))
    {
	    $model->setData($data);
    }

    Mage::register("subscriptions_data", $model);

    $this->loadLayout();
    $this->_setActiveMenu("pagarme/subscriptions");

    $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

    $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Subscriptions Manager"), Mage::helper("adminhtml")->__("Subscriptions Manager"));
    $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Subscriptions Description"), Mage::helper("adminhtml")->__("Subscriptions Description"));


    $this->_addContent($this->getLayout()->createBlock("pagarme/adminhtml_subscriptions_edit"))->_addLeft($this->getLayout()->createBlock("pagarme/adminhtml_subscriptions_edit_tabs"));

    $this->renderLayout();
}

public function saveAction()
{
	$post_data=$this->getRequest()->getPost();

	if ($post_data)
    {
        $api_mode = Mage::getStoreConfig('payment/pagarme_settings/mode');
        $api_key = Mage::getStoreConfig('payment/pagarme_settings/apikey_' . $api_mode);
        Pagarme::setApiKey($api_key);

        $post_data['payment_methods'] = implode(',', $post_data['payment_methods']);

		try
        {
            $plan = new PagarMe_Plan(array_merge($post_data, array(
                'amount' => intval(floatval($post_data['amount']) * 100)
            )));
            $result = $plan->create();

            $post_data['remote_id'] = $result['id'];
            if(empty($post_data['charges'])) $post_data['charges'] = new Zend_Db_Expr('NULL');

			$model = Mage::getModel("pagarme/subscriptions")
			->addData($post_data)
			->setId($this->getRequest()->getParam("id"))
			->save();

			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Subscriptions was successfully saved"));
			Mage::getSingleton("adminhtml/session")->setSubscriptionsData(false);

			if ($this->getRequest()->getParam("back")) {
				$this->_redirect("*/*/edit", array("id" => $model->getId()));
				return;
			}
			$this->_redirect("*/*/");
			return;
		} 
		catch (Exception $e)
        {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			Mage::getSingleton("adminhtml/session")->setSubscriptionsData($this->getRequest()->getPost());
			$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));

		    return;
		}
	}

	$this->_redirect("*/*/");
}

public function deleteAction()
{
	if( $this->getRequest()->getParam("id") > 0 )
    {
		try
        {
			$model = Mage::getModel("pagarme/subscriptions");
			$model->setId($this->getRequest()->getParam("id"))->delete();
			Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
			$this->_redirect("*/*/");
		} 
		catch (Exception $e)
        {
			Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
			$this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
		}
	}

	$this->_redirect("*/*/");
}

public function massRemoveAction_()
{
	try
    {
		$ids = $this->getRequest()->getPost('ids', array());
		foreach ($ids as $id)
        {
              $model = Mage::getModel("pagarme/subscriptions");
			  $model->setId($id)->delete();
		}
		Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
	}
	catch (Exception $e)
    {
		Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
	}

	$this->_redirect('*/*/');
}
	
/**
 * Export order grid to CSV format
 */
public function exportCsvAction_()
{
	$fileName   = 'subscriptions.csv';
	$grid       = $this->getLayout()->createBlock('pagarme/adminhtml_subscriptions_grid');
	$this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
} 
/**
 *  Export order grid to Excel XML format
 */
public function exportExcelAction_()
{
	$fileName   = 'subscriptions.xml';
	$grid       = $this->getLayout()->createBlock('pagarme/adminhtml_subscriptions_grid');
	$this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
}

}

