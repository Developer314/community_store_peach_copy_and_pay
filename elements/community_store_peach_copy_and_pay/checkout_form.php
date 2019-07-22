<?php defined('C5_EXECUTE') or die("Access Denied.");
extract($vars);
$transactionFailed = false;
$transactionFailedMsg = "";
$tFailedData = Session::get('PeachPay_Copy_And_Pay_Error');
if(isset($tFailedData)){
	$errorMsg = $tFailedData["message"];
	$transactionFailedMsg = "$errorMsg. Please try again.";
	$transactionFailed = true;
	Session::remove('PeachPay_Copy_And_Pay_Error');
}
?>
<div class="peachpayment_cap"></div>
<style>
	.wpwl-container{
		position: fixed;
	    top: 100px;
	    left: 0;
	    right: 0;
		z-index: 9999;
	}
	.peachpayment_cap .tran-error {
		background-color: #de3232d9;
	    padding: 3px 15px 0px 15px;
	    margin-bottom: 25px;
	    border-radius: 3px;
	    -webkit-box-shadow: 0px 2px 13px -8px rgb(74, 71, 74);
	    -moz-box-shadow: 0px 2px 13px -8px rgb(74, 71, 74);
	    box-shadow: 0px 2px 13px -8px rgb(74, 71, 74);
	    margin-top: 20px;
	}
	.peachpayment_cap .tran-error h2{
		color: #fff;
	    margin: 0px;
	}
	.peachpayment_cap .tran-error p{
		color: #fff;
	    font-size: 16px;
	    font-weight: 500;
	}
	.wpwl-has-error{
		color: #a94442;
	    position: fixed;
	    left: 0px;
	    right: 0px;
	    background-color: #ea1a25;
	    bottom: 0px;
	    color: #fff !important;
	    padding: 15px;
	}
	    
</style>


<script>
	$( document ).ready(function() {
		<? if($transactionFailed){ ?>
			$(".store-payment-errors").html("<?php echo $transactionFailedMsg;?>");
		<?php } else{?>
			$(".store-payment-errors").addClass("hide");
		<?php } ?>
		$('.store-btn-complete-order').on('click', function (e) {
			$(".peachpayment_cap").html("");
            // Open Checkout with further options
            var currentpmid = $('input[name="payment-method"]:checked:first').data('payment-method-id');

            if (currentpmid == <?= $pmID; ?>) {
                $(this).prop('disabled', true);
                $(this).val('<?= t('Processing...'); ?>');
				var paymentform = $('#store-checkout-form-group-payment');
				//pmaction = paymentform.attr('action')
				pmaction = CCM_APPLICATION_URL+'/peach_payment_redirect';
				var data = paymentform.serialize();
				//console.log(pmaction);
				$.ajax({
                    url: paymentform.attr('action'),
                    type: 'post',
                    cache: false,
                    data: data,
                    dataType: 'text',
                    success: function(data) {
						$.ajax({
		                    url: CCM_APPLICATION_URL+"/checkout/creapaymentSession",
		                    type: 'post',
		                    cache: false,
		                    success: function(data) {
			                    data = JSON.parse(data);
			                    var htmlStr = "";
			                    if(data.status == 1){
				                    htmlStr = '<form action="'+pmaction+'" class="paymentWidgets" data-brands="VISA MASTER AMEX VISAELECTRON DISCOVER MAESTRO CARTEBLEUE"></form>';
				                    $("body").append(htmlStr);
				                    var tag = document.createElement("script");
									tag.src = data.pmPath+'/v1/paymentWidgets.js?checkoutId='+ data.result.id;
									document.getElementsByTagName("head")[0].appendChild(tag);
			                    }else{
				                    htmlStr += '<div class="row tran-error">';	
				                    htmlStr += '	<div class="col-md-12">';
				                    htmlStr += '		<h2> Transaction Failed </h2>';
				                    htmlStr += '		<p>'+ data.result.result.description +'</p>';
				                    htmlStr += '	</div>';
				                    htmlStr += '</div>';
									$('.store-btn-complete-order').val('<?= t('Complete Order'); ?>');
									$('.store-btn-complete-order').prop('disabled', false);
				                    $(".peachpayment_cap").html(htmlStr);
			                    }
			                   
		                    }
		                });
		            }
		        });
                e.preventDefault();
            }
        });
	});
</script>