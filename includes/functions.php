<?php
function wpec_save_virtualmerchant_settings() {
	$options = get_option( 'wpsc_vmerchnat' );
	
	foreach( $_POST['wpsc_vmerchnat'] as $name => $value ) {
		$options[$name] = rtrim($value);
	}
	
	update_option( 'wpsc_vmerchnat', $options );
	
	return true;	
}

function wpec_virtualmerchant_settings_form() {
	$args = array(
		'user_id'      => '',
		'merchant_id'  => '',
		'pin'          => '',
		'avs'          => 'no',
		'mode'         => 'live'
	);
	  add_option( 'wpsc_vmerchnat', $args );
	  $options = get_option( 'wpsc_vmerchnat' );
	$output = '
		<tr>
			<td>
			  '. __( 'Account ID', 'wpsc_gold_cart' ) .'
			</td>
			<td>
			  <input type="text" value="'.$options['merchant_id'].'" name="wpsc_vmerchnat[merchant_id]"  />
			</td>
		</tr>
		<tr>
			<td>
			  ' . __( 'User ID', 'wpsc_gold_cart' ) .'
			</td>
			<td>
			  <input type="text" value="'.$options['user_id'].'"  name="wpsc_vmerchnat[user_id]"  />
			</td>
		</tr>
		<tr>
			<td>
			  ' . __( 'Merchant Pin', 'wpsc_gold_cart' ) .'
			</td>
			<td>
			  <input type="text" value="'.$options['pin'].'"  name="wpsc_vmerchnat[pin]"  />
			</td>
		</tr>
		<tr>
			<td>
			  ' . __( 'AVS Security', 'wpsc_gold_cart' ) .'
			</td>
			<td>
			 <input type="radio" value="yes" name="wpsc_vmerchnat[avs]"  ' . checked( 'yes',$options['avs'],false ) .'  /><label> ' .  TXT_WPSC_YES . ' </label>
			 <input type="radio" value="no"  name="wpsc_vmerchnat[avs]"  ' . checked( 'no', $options['avs'],false ) .'  /><label> ' .  TXT_WPSC_NO . '</label>
			</td>
		</tr>
		<tr>
			<td>
			  ' . __( 'Mode', 'wpsc_gold_cart' ) .'
			</td>
			<td>
			  <input type="radio" value="live" name="wpsc_vmerchnat[mode]"  ' .  checked( 'live', $options['mode'],false ) .'  /><label> ' .  __( 'Live Mode', 'wpsc_gold_cart' ) . ' </label>
			  <input type="radio" value="test"  name="wpsc_vmerchnat[mode]"  ' . checked( 'test', $options['mode'],false ) .'  /><label> ' . __( 'Test Mode', 'wpsc_gold_cart' ) . '</label>
			</td>
		</tr>
	';
  $struc = get_option('permalink_structure');
  if ( $struc == '' ) {
    $output .= '
    <tr>
      <td colspan="2">
        <strong style="color:red;">'.__( 'This Gateway will only work if you change your permalink structure do anything except the default setting. In Settings->Permalinks', 'wpsc_gold_cart' ).'</strong>
      </td>
    </tr>
    ';
   }
  return $output;
}

function wpec_vmerchant_checkout_fields() {
	global $gateway_checkout_form_fields;
	if( in_array( 'wpec_virtualmerchant', (array) get_option('custom_gateway_options') ) ) {
		
		$curryear = date( 'Y' );
		$curryear_2 = date( 'y' );
		$years = '';
		//generate year options
		for ( $i = 0; $i < 10; $i++ ) {
			$years .= "<option value='" . $curryear_2 . "'>" . $curryear . "</option>\r\n";
			$curryear++;
			$curryear_2++;
		}
		ob_start(); ?>
		<tr>
			<td class="wpsc_CC_details"> <?php _e( 'Credit Card Number *', 'wpsc' ); ?></td>
			<td>
				<input type="text" value='' name="CardNumber" />
			</td>
		</tr>
		<tr>
			<td class='wpsc_CC_details'><?php _e( 'Credit Card Expiry *', 'wpsc' ); ?></td>
			<td>
				<select class='wpsc_ccBox' name='ExpiryMonth'>
					<option value='01'>01</option>
					<option value='02'>02</option>
					<option value='03'>03</option>
					<option value='04'>04</option>
					<option value='05'>05</option>
					<option value='06'>06</option>
					<option value='07'>07</option>
					<option value='08'>08</option>
					<option value='09'>09</option>
					<option value='10'>10</option>
					<option value='11'>11</option>
					<option value='12'>12</option>
				</select>
				<select class='wpsc_ccBox' name='ExpiryYear'>
					<?php echo $years; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td class='wpsc_CC_details'><?php _e( 'CVC *', 'wpsc' ); ?></td>
			<td><input type='text' size='4' value='' maxlength='4' name='Cvc2' /></td>
		</tr>
		<?php
		$gateway_checkout_form_fields['wpec_virtualmerchant'] = ob_get_clean();
	}
}
add_action( 'wpsc_init', 'wpec_vmerchant_checkout_fields' );