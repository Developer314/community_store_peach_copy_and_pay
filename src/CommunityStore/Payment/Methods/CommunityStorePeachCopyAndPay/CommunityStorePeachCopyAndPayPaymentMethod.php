<?php
namespace Concrete\Package\CommunityStorePeachCopyAndPay\Src\CommunityStore\Payment\Methods\CommunityStorePeachCopyAndPay;


use Concrete\Package\CommunityStore\Controller\SinglePage\Dashboard\Store;
use Core;
use Config;
use Exception;
use Session;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Order\Order as StoreOrder;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Payment\Method as StorePaymentMethod;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Utilities\Calculator as StoreCalculator;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Customer\Customer as StoreCustomer;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Shipping\Method\ShippingMethod as StoreShippingMethod;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Cart\Cart as StoreCart;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOption as StoreProductOption;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Product\ProductOption\ProductOptionItem as StoreProductOptionItem;
use \Concrete\Package\CommunityStore\Src\CommunityStore\Order\OrderStatus\OrderStatus as StoreOrderStatus;



class CommunityStorePeachCopyAndPayPaymentMethod extends StorePaymentMethod
{
    private function getCurrencies()
    {
        return [
            'ZAR' => t('South African rand'),
            'USD' => t('United States Dollar'),
            'CAD' => t('Canadian Dollar'),
            'CHF' => t('Swiss Franc'),
            'DKK' => t('Danish Krone'),
            'EUR' => t('Euro'),
            'GBP' => t('Pound Sterling'),
            'NOK' => t('Norwegian Krone'),
            'PLN' => t('Polish Zloty'),
            'SEK' => t('Swedish Krona'),
            'AUD' => t('Australian Dollar'),
            'NZD' => t('New Zealand Dollar')
        ];
    }
    
    private function getPaymentType(){
	    
        return [
            'PA' => t('Preauthorization'),
            'DB' => t('Debit'),
            'CD' => t('Credit'),
            'CP' => t('Capture'),
            'RV' => t('Reversal'),
            'RF' => t('Refund')
        ];
        
    }


    public function dashboardForm()
    {
        $this->set('peachPayMode', Config::get('community_store_peach_pay.mode'));
        $this->set('peachPayCurrency', Config::get('community_store_peach_pay.currency'));
        $this->set('peachPayPaymentType', Config::get('community_store_peach_pay.paymentType'));
        $this->set('peachPayTokenApiKey', Config::get('community_store_peach_pay.tokenApiKey'));
        $this->set('peachPayEntityId', Config::get('community_store_peach_pay.entityId'));
        $this->set('peachPayEntityIdRecurring', Config::get('community_store_peach_pay.entityIdRecurring'));
        $this->set('peachPayeCheckoutUrl', Config::get('community_store_peach_pay.checkoutUrl'));
        $this->set('peachPayeTestUrl', Config::get('community_store_peach_pay.testUrl') );
        $this->set('peachPayeLiveUrl', Config::get('community_store_peach_pay.liveUrl'));
        $this->set('peachPayDebugLogMode', Config::get('community_store_peach_pay.peachPayDebugLogMode'));
        
        $this->set('peachPayTokenTestApiKey', Config::get('community_store_peach_pay.testTokenApiKey'));
        $this->set('peachPayTestEntityId', Config::get('community_store_peach_pay.testEntityId'));
        $this->set('peachPayTestEntityIdRecurring', Config::get('community_store_peach_pay.testEntityIdRecurring'));
        
        $this->set('form', Core::make("helper/form"));
        $this->set('peachPayCurrencies', $this->getCurrencies());
        $this->set('peachPayPaymentTypes', $this->getPaymentType());
    }

    public function save(array $data = [])
    {
        Config::save('community_store_peach_pay.mode', $data['peachPayMode']);
        Config::save('community_store_peach_pay.currency', $data['peachPayCurrency']);
        Config::save('community_store_peach_pay.paymentType', $data['peachPayPaymentType']);
        Config::save('community_store_peach_pay.tokenApiKey', $data['peachPayTokenApiKey']);
        Config::save('community_store_peach_pay.entityId', $data['peachPayEntityId']);
        Config::save('community_store_peach_pay.entityIdRecurring', $data['peachPayEntityIdRecurring']);
        Config::save('community_store_peach_pay.checkoutUrl', $data['peachPayeCheckoutUrl']);
        Config::save('community_store_peach_pay.testUrl', $data['peachPayeTestUrl']);
        Config::save('community_store_peach_pay.liveUrl', $data['peachPayeLiveUrl']);
        Config::save('community_store_peach_pay.peachPayDebugLogMode', $data['peachPayDebugLogMode']);
        
        
        Config::save('community_store_peach_pay.testTokenApiKey', $data['peachPayTokenTestApiKey']);
        Config::save('community_store_peach_pay.testEntityId', $data['peachPayTestEntityId']);
        Config::save('community_store_peach_pay.testEntityIdRecurring', $data['peachPayTestEntityIdRecurring']);
    }

    public function validate($args, $e)
    {
        return $e;
    }

    public function checkoutForm()
    {
        $mode = Config::get('community_store_peach_pay.mode');
        $amount = number_format(StoreCalculator::getGrandTotal(), 2, '.', '');
        $this->set('mode', $mode);
        $this->set('currency', Config::get('community_store_peach_pay.currency'));
        $this->set('paymentType', Config::get('community_store_peach_pay.paymentType'));
        $this->set('amount', $amount);

        if ($mode == 'live') {
            $this->set('peachPayUrl', Config::get('community_store_peach_pay.liveUrl'));
        } else {
            $this->set('peachPayUrl', Config::get('community_store_peach_pay.testUrl'));
        }
        $pmID = StorePaymentMethod::getByHandle('community_store_peach_copy_and_pay')->getID();
        $this->set('pmID', $pmID);
        


    }
    
    public function submitPayment()
    {
        return ['error' => 0, 'transactionReference' => ''];
    }

    public function getPaymentMinimum()
    {
        return 0.5;
    }

    public function getName()
    {
        return 'Peach Payment';
    }
    public function creapaymentSession(){
	    
        $amount = number_format(StoreCalculator::getGrandTotal() , 2, '.', '');
        $peachPayUrl = "";
        $checkoutUrl = Config::get('community_store_peach_pay.checkoutUrl');
        $entityID = Config::get('community_store_peach_pay.entityId');
        $curency = Config::get('community_store_peach_pay.currency');
        $token = Config::get('community_store_peach_pay.tokenApiKey');
        $paymentType = Config::get('community_store_peach_pay.paymentType');
        $order = StoreOrder::getByID(Session::get('orderID'));
        $logMode = Config::get('community_store_peach_pay.peachPayDebugLogMode');
        $sslVerify = false;
        $mode = Config::get('community_store_peach_pay.mode');
        if ($mode == 'live') {
            $peachPayUrl = Config::get('community_store_peach_pay.liveUrl');
            $sslVerify = true;
        } else {
            $peachPayUrl = Config::get('community_store_peach_pay.testUrl');
			$entityID = Config::get('community_store_peach_pay.testEntityId');
			$token = Config::get('community_store_peach_pay.testTokenApiKey');
            $sslVerify = false;
        }
        $url = $peachPayUrl.$checkoutUrl;
        $data = "entityId=$entityID" .
                    "&amount=$amount" .
                    "&currency=".$curency .
                    "&paymentType=$paymentType";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Authorization:Bearer '.$token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerify);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $res = json_decode($responseData);
        $status = 0;
        if($res->result->code == "000.200.100"){
            $status = 1;
			Session::set('PeachPay_Copy_And_Pay', [
	            'client_reference_id' => $order->getOrderID(),
	            'payment_reference_id' => $res->id,
	            'success_url' => '/checkout/complete',
	            'cancel_url' => 'checkout/failed#payment',
	        ]);
        }
       
		if($logMode == "yes"){
			$log_data = array(
				"client_reference_id" => $order->getOrderID(),
				"amount"			  => $amount,
				"checkout_response_data" => $res
			);
			\Log::addDebug('Peach Payament Copy And Pay Checkout Details ::'.json_encode($log_data));
		}
        
        $rtnVal = array(
            "status" => $status,
            "result" => $res,
            "pmPath" => $peachPayUrl
        );
        die(json_encode($rtnVal));
    }

    public function getStatus(){
	    
		$peachCAP_Session = Session::get('PeachPay_Copy_And_Pay');
        $logMode = Config::get('community_store_peach_pay.peachPayDebugLogMode');
		$success = false;
		$errorMsg = "Transaction Failed.";
		if($peachCAP_Session != NULL){
			$orderID = $peachCAP_Session["client_reference_id"];
			$payment_reference_id = $peachCAP_Session["payment_reference_id"];
			$successUrl = $peachCAP_Session["success_url"];
			$canelUrl = $peachCAP_Session["cancel_url"];
			if(!empty($_GET)){
				$paymentID = $_GET["id"];
				$resourcePath = $_GET["resourcePath"];
				if(isset($paymentID)){
					
					$peachPayUrl = "";
			        $entityID = Config::get('community_store_peach_pay.entityId');
			        $token = Config::get('community_store_peach_pay.tokenApiKey');
					$mode = Config::get('community_store_peach_pay.mode');
			        $sslVerify = false;
			        if ($mode == 'live') {
			            $peachPayUrl = Config::get('community_store_peach_pay.liveUrl').$resourcePath;
			            $sslVerify = true;
			        } else {
			            $peachPayUrl = Config::get('community_store_peach_pay.testUrl').$resourcePath;
						$entityID = Config::get('community_store_peach_pay.testEntityId');
						$token = Config::get('community_store_peach_pay.testTokenApiKey');
			            $sslVerify = false;
			        }
			        $peachPayUrl .= "?entityId=".$entityID;
			        $ch = curl_init();
			        curl_setopt($ch, CURLOPT_URL, $peachPayUrl);
			        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			                    'Authorization:Bearer '.$token));
			        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerify);// this should be set to true in production
			        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			        $responseData = curl_exec($ch);
			        if(curl_errno($ch)) {
			            return curl_error($ch);
			        }
			        curl_close($ch);
			        $res = json_decode($responseData);
			        if($res->result->code == "000.000.000" || $res->result->code == "000.100.110"){
				        
		                $order = StoreOrder::getByID($orderID);
		
		                if ($order) {
		                    $order->completeOrder($res->id);
		                    $order->updateStatus(StoreOrderStatus::getStartingStatus()->getHandle());
		                    $success = true;
		                }
			        }
			        else{
				        $success = false;
				        $errorMsg = $res->result->description;
			        }
			        
					if($logMode == "yes"){
						$log_data = array(
							"client_reference_id" => $orderID,
							"payment_transaction_response_data" => $res
						);
						\Log::addDebug('Peach Payament Copy And Pay Transaction Details ::'.json_encode($log_data));
					}
			        
				}
				
			}
			Session::remove('PeachPay_Copy_And_Pay');
			if($success){
				$this->redirect($successUrl);
			}
			else{
				Session::set('PeachPay_Copy_And_Pay_Error', [
		            'message' => $errorMsg
		        ]);
				$this->redirect($canelUrl);
			}
			
		}
        die;
    }

    public function isExternal()
    {
        return true;
    }
}

return __NAMESPACE__;
