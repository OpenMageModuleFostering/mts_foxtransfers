<?php
class MTS_Foxtransfers_PayController extends Mage_Core_Controller_Front_Action{
	
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

	
    public function redirectAction()
	{
	
		$FoxUser = Mage::getStoreConfig('payment/foxtransfers/username');
		$action = Mage::getStoreConfig('payment/foxtransfers/action');
		$private_key = Mage::getStoreConfig('payment/foxtransfers/privatekeycontent');
		$send_email = Mage::getStoreConfig('payment/foxtransfers/sendemail');

     	include_once(Mage::getBaseDir() . '/app/code/local/MTS/Foxtransfers/lib/FoxTransfersShop.class.php');
		
     	$a = $this->getQuote()->getShippingAddress();
		$b = $this->getQuote()->getBillingAddress();
		
		$cc = $this->getQuote()->getBaseCurrencyCode();
		$cost = $a->getBaseSubtotal() - $a->getBaseDiscountAmount();
		$shipping = $a->getBaseShippingAmount();

		$_shippingTax = $this->getQuote()->getShippingAddress()->getBaseTaxAmount();
		$_billingTax = $this->getQuote()->getBillingAddress()->getBaseTaxAmount();
		
		$tax = sprintf('%.2f', $_shippingTax + $_billingTax);
		$cost = sprintf('%.2f', $cost + $tax);
        if (!isset($_SESSION['checkout']['last_real_order_id'])) $_SESSION['checkout']['last_real_order_id'] = 0;

		$session = Mage::getSingleton('checkout/session');
		$session->setPayQuoteId($session->getQuoteId());
		$session->unsQuoteId();
        
		$order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
      	
		
		if($order->getId())
        {
		
            $total = $order->getBaseTotalDue();
            $order_id = $order->getRealOrderId();
			
            $FoxShop = new FoxTransfersShop($private_key);
			
            $FoxShopReq = array(
				'partner'			=>		$FoxUser,
				'amount'			=>		$total,
				'currency'			=>		Mage::app()->getStore()->getCurrentCurrencyCode(),
				'transaction'		=>		$order_id,
				'lang'				=>		Mage::app()->getLocale()->getLocaleCode(),
            );
			if ($send_email) {
				$FoxShopReq['snd_email'] =	$order->getCustomerEmail();
			}

			
			//Mage::log('FoxTransfers: '.$arr['partner'].'|'.$arr['amount'].'|'.$arr['currency'].'|'.$arr['transaction']);
			
			$FoxShopReqWithSign = $FoxShop->generateForm($FoxShopReq);
			
			$form = '<form id="foxtransfers-form" action="' . $action . '" method="post">';
			$form .= $FoxShopReqWithSign;
			//$form .= '</form>';
			$form .= '</form><script> onload = function () { document.getElementById("foxtransfers-form").submit(); } </script>';
			
			echo $form;
			
		}
        
		else 
		{
        	$this->_redirect('checkout/onepage/failure');
        }
		
		return;
		
    }
    
	public function successAction(){
	
		$foxtransfers_key = Mage::getStoreConfig('payment/foxtransfers/foxtransferskeycontent');
		include_once(Mage::getBaseDir() . '/app/code/local/MTS/Foxtransfers/lib/FoxTransfersShop.class.php');
	
		$post = $this->getRequest()->getPost();
		
		//	Because at the moment, when customer returns from bank, there is no garantee, that payment was successful
		//	 - we do not change order status
		//	 - we do this when real report comes in report action

		//	Just send customer email

		$order_id = $post['transaction'];
		$order = Mage::getModel('sales/order');
		$order -> loadByIncrementId($order_id);
		$order -> sendNewOrderEmail();
		
		$session = $this->getCheckout();
        $session -> setQuoteId($session -> getPayQuoteId(true));
        $session -> getQuote() -> setIsActive(false) -> save();
        $this -> _redirect('checkout/onepage/success', array('_secure' => true));
		
		return;
		
		//	To change the order status
		
		/*
		$FoxShop = new FoxTransfersShop($foxtransfers_key);
     	if ($FoxShop->validateDataSuccess($post)){
     		$order_id=$post['transaction'];
     		$order = Mage::getModel('sales/order');
     		$order->loadByIncrementId($order_id)->setStatus('processing')->save();
     		$order->sendNewOrderEmail();
     		Mage::getSingleton('checkout/session')->unsQuoteId();
     		$this->_redirect('checkout/onepage/success');
     	}
     	else {
     		Mage::getSingleton('checkout/session')->unsQuoteId();
     		$this->_redirect('checkout/onepage/failure');
     	}
		*/
		
    }
	
	public function cancelAction(){
	
		$foxtransfers_key = Mage::getStoreConfig('payment/foxtransfers/foxtransferskeycontent');
		include_once(Mage::getBaseDir() . '/app/code/local/MTS/Foxtransfers/lib/FoxTransfersShop.class.php');
	
     	$post = $this->getRequest()->getPost();

		$FoxShop = new FoxTransfersShop($foxtransfers_key);
		
     	if ($FoxShop->validateDataSuccess($post)==0){
     		$order_id = $post['transaction'];
     		$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($order_id)->cancel()->save();
     		Mage::getSingleton('checkout/session')->unsQuoteId();
     	
		}
		
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPayQuoteId(true));
		
		$this->_redirect('checkout/cart');
		//$this->_redirect('checkout/onepage/failure');
		
    }
	
	public function reportAction()
	{
 
		$foxtransfers_key = Mage::getStoreConfig('payment/foxtransfers/foxtransferskeycontent');
		include_once(Mage::getBaseDir() . '/app/code/local/MTS/Foxtransfers/lib/FoxTransfersShop.class.php');

		$post = $this->getRequest()->getPost();
		
		$FoxShop = new FoxTransfersShop($foxtransfers_key);
		
     	if ($FoxShop->validateData($post)) {
		
     		$order_id = $post['transaction'];
     		$order = Mage::getModel('sales/order');
     		if ($post['status']){
	     		$order->loadByIncrementId($order_id)->setStatus('processing')->save();
				$order->sendNewOrderEmail();
	     		echo 'OK';
				exit;
     		}
     		else {
				$order->loadByIncrementId($order_id)->cancel()->save();
     			echo 'OK';
				exit;
     		}
			
     	}else {
		
     		echo 'NOT OK';
			exit;
			
     	}
		
    }
	
}