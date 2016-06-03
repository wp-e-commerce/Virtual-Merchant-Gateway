<?php
class wpec_merchant_virtualmerchant extends wpsc_merchant {
	
	public function submit(){

		// basic credit card verification
		$errorMsg = "";

		if ( isset( $_POST['CardNumber'] ) && strlen( $_POST['CardNumber'] ) > 0 ) {
		  $CardNumber = $_POST['CardNumber'];
		} else {
		  $errorMsg .= __( 'Credit Card Number Required', 'wpsc_gold_cart' ) . '<br/>';
		}

		if ( isset( $_POST['ExpiryMonth'] ) && strlen( $_POST['ExpiryMonth'] ) > 0 ) {
		  $ExpiryMonth = $_POST['ExpiryMonth'];
		} else {
		  $errorMsg .= __( 'Credit Card Expiry Month Required', 'wpsc_gold_cart' ) . '<br/>';
		}

		if ( isset( $_POST['ExpiryYear'] ) && strlen( $_POST['ExpiryYear'] ) > 0 ) {
		  $ExpiryYear = $_POST['ExpiryYear'];
		} else {
		  $errorMsg .= __( 'Credit Card Expiry Year Required', 'wpsc_gold_cart' ) . '<br/>';
		}

		if ( isset( $_POST['Cvc2'] ) && strlen( $_POST['Cvc2'] ) > 0 ) {
		  $Cvc2 = $_POST['Cvc2'];
		} else {
		  $errorMsg .= __( 'Credit Card Cvc2 code Required', 'wpsc_gold_cart' ) . '<br/>';
		}

		if ( strlen( $errorMsg ) > 0 ) {
		  $this->set_error_message( $errorMsg );
		  header( 'Location: '.$this->cart_data['shopping_cart_url'] );
		  exit();
		}

		$options  = get_option( 'wpsc_vmerchnat' );

		// temp vars to make things easier
		if ( get_option('permalink_structure') != '' ) {
		  $separator ="?";
		} else {
		  $separator ="&";
		}

		if ( $options['mode'] == 'test' ) {
		  // test url goes here
		  $url = 'https://demo.myvirtualmerchant.com/VirtualMerchantDemo/process.do';
		} else {
		  //live url goes here
		  $url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
		}

		$amount                   = number_format( $this->cart_data['total_price'], 2, '.', '' );
		$sales_tax                = $this->cart_data['cart_tax'];
		$invoice_number           = $this->cart_data['session_id'];
		$email                    = $this->cart_data['email_address'];
		$transaction_results_page = $this->cart_data['transaction_results_url'];
		$credit_card_date         = $ExpiryMonth . '' . $ExpiryYear;

		// optional vars
		$first_name               = $this->cleanInput($this->cart_data['billing_address']['first_name']);
		$last_name                = $this->cleanInput($this->cart_data['billing_address']['last_name']);
		$address2                 = $this->cleanInput($this->cart_data['billing_address']['address']);
		$city                     = $this->cleanInput($this->cart_data['billing_address']['city']);
		$state                    = $this->cleanInput($this->cart_data['billing_address']['state']);
		$country                  = $this->cart_data['billing_address']['country'];

		// avs vars
		if ( $options['avs'] == 'yes' ) {
		  $avs_zip                = $this->cart_data['billing_address']['post_code'];
		  $avs_address            = $this->cleanInput($this->cart_data['billing_address']['address']);
		}

		$form = '
		  <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
		  <html lang="en">
			<head>
			  <title></title>
			</head>
			<body>
			  <form id="vmerchant_form" action="' .$url . '" method="POST">
				<input type="hidden" name="ssl_transaction_type"       value="ccsale">
				<input type="hidden" name="ssl_show_form"              value="false">
				<input type="hidden" name="ssl_merchant_id"            value="'. $options['merchant_id'] .'">
				<input type="hidden" name="ssl_user_id"                value="'. $options['user_id'] .'">
				<input type="hidden" name="ssl_pin"                    value="'. $options['pin'] .'">
				<input type="hidden" name="ssl_amount"                 value="'. $amount .'">
				<input type="hidden" name="ssl_salestax"               value="'. $sales_tax .'">
				<input type="hidden" name="ssl_invoice_number"         value="'. $invoice_number . '">
				<input type="hidden" name="ssl_email"                  value="'. $email . '">
				<input type="hidden" name="ssl_card_number"            value="'. $CardNumber . '">
				<input type="hidden" name="ssl_exp_date"               value="'. $credit_card_date . '">
				<input type="hidden" name="ssl_cvv2cvc2_indicator"     value="1">
				<input type="hidden" name="ssl_cvv2cvc2"               value="'. $Cvc2 . '">
				<input type="hidden" name="ssl_receipt_decl_get_url"   value="'. $transaction_results_page . '">
				<input type="hidden" name="ssl_receipt_apprvl_get_url" value="'. $transaction_results_page . '' .$separator .'">
				<input type="hidden" name="ssl_result_format"          value="HTML">
				<input type="hidden" name="ssl_receipt_decl_method"    value="REDG">
				<input type="hidden" name="ssl_receipt_apprvl_method"  value="REDG">
				<input type="hidden" name="ssl_customer_code"          value="1111">';
		if ( strlen( $first_name ) > 0 ){
		  $form .= '<input type="hidden" name="ssl_first_name" value="' . $first_name . '">';
		}

		if ( strlen( $last_name ) > 0 ) {
		  $form .= '<input type="hidden" name="ssl_last_name" value="' . $last_name . '">';
		}

		if ( strlen( $address2 ) > 0 ) {
		  $form .= '<input type="hidden" name="ssl_address2" value="' . $address2 . '">';
		}

		if ( strlen( $city ) > 0 ) {
		  $form .= '<input type="hidden" name="ssl_city" value="' . $city . '">';
		}

		if ( strlen( $state ) > 0 ) {
		  $form .= '<input type="hidden" name="ssl_state" value="' . $state . '">';
		}

		if ( strlen( $country ) > 0 ) {
			$form .= '<input type="hidden" name="ssl_country" value="' . $country. '">';
		}

		if ( $options['mode'] == 'test' ) {
		  $form .= '<input type="hidden" name="ssl_test_mode" value="true">';
		} else {
		  $form .= '<input type="hidden" name="ssl_test_mode" value="false">';
		}

		if ( $options['avs'] == 'yes' ) {
		  $form .= '<input type="hidden" name="ssl_avs_address" value="' . $avs_address . '">
					<input type="hidden" name="ssl_avs_zip" value="' . $avs_zip . '">';
		}

		$form .= '
			  </form>
			  <script type="text/javascript">document.getElementById("vmerchant_form").submit();</script>
			</body>
		  </html>';

		echo $form;
		exit();
	}
	
	private function cleanInput($strRawText){
		$iCharPos = 0;
		$chrThisChar = "";
		$strCleanedText = "";
		$strAllowableChars     = "0123456789 ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_/\(),.:|";
		$blnAllowAccentedChars = TRUE;

		//Compare each character based on list of acceptable characters
		while ( $iCharPos < strlen( $strRawText ) ) {
		  // Only include valid characters **
		  $chrThisChar = substr($strRawText, $iCharPos, 1);
		  if ( strpos( $strAllowableChars, $chrThisChar ) !== FALSE ) {
			  $strCleanedText = $strCleanedText . $chrThisChar;
		  } elseIf ( $blnAllowAccentedChars == TRUE ) {
			// Allow accented characters and most high order bit chars which are harmless **
			if ( ord( $chrThisChar ) >= 191 ) {
				$strCleanedText = $strCleanedText . $chrThisChar;
			}
		  }

		  $iCharPos = $iCharPos + 1;
		}

	return $strCleanedText;
	}
}

if ( isset( $_GET['ssl_card_number'] ) &&
     isset( $_GET['ssl_exp_date'] ) &&
     isset( $_GET['ssl_amount'] ) &&
     isset( $_GET['ssl_invoice_number']) &&
     isset( $_GET['ssl_result_message'] ) &&
     isset( $_GET['ssl_txn_id'] ) &&
     isset( $_GET['ssl_approval_code'] ) &&
     isset( $_GET['ssl_cvv2_response'] ) &&
     isset( $_GET['ssl_txn_time'] ) ) {
	add_action('init', 'wpec_vmerchant_ipn');
}

function wpec_vmerchant_ipn() {

	$sessionid = $_GET['ssl_invoice_number'];

	if ( $_GET['ssl_result_message'] == 'APPROVED' || $_GET['ssl_result_message'] == 'APPROVAL' ) {
		// success
		$purchase_log = new WPSC_Purchase_Log( $sessionid, 'sessionid' );
		$purchase_log->set( array(
		  'processed' => WPSC_Purchase_Log::ACCEPTED_PAYMENT,
		  'transactid' => $_GET['ssl_txn_id'],
		  'notes' => 'Virtual Merchant time : "' . $_GET['ssl_txn_time'] . '"',
		) );
		$purchase_log->save();

		// set this global, wonder if this is ok
		transaction_results( $sessionid, true );
	} else {
		// success
		$purchase_log = new WPSC_Purchase_Log( $sessionid, 'sessionid' );
		$purchase_log->set( array(
		  'processed' => WPSC_Purchase_Log::INCOMPLETE_SALE,
		  'transactid' => $_GET['ssl_txn_id'],
		  'notes' => 'Virtual Merchant time : "' . $_GET['ssl_txn_time'] . '"',
		) );
		$purchase_log->save();
		$error_messages = wpsc_get_customer_meta( 'checkout_misc_error_messages' );
		if ( ! is_array( $error_messages ) )
			$error_messages = array();
		$error_messages[] = '<strong style="color:red">' . urldecode( $_GET['ssl_result_message'] ) . ' </strong>';
		wpsc_update_customer_meta( 'checkout_misc_error_messages', $error_messages );
		$checkout_page_url = get_option( 'shopping_cart_url' );
		
		if ( $checkout_page_url ) {
			header( 'Location: '.$checkout_page_url );
			exit();
		}
	}
}
?>