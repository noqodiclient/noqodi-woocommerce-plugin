<?php
/*
 * Plugin Name: noqodi Payment Gateway
 * Plugin URI: https://rudrastyh.com/woocommerce/payment-gateway-plugin.html
 * Description: This plugin allows for Noqodi payment gateway system
 * Author: Sushma Rama
 * Author URI: https://www.noqodi.com
 * Version: 0.1.0
 */


if( ! in_array( 'woocommerce/woocommerce.php', apply_filters(
'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'noqodi_payment_init', 11);

function noqodi_payment_init() {
	if( class_exists( 'WC_Payment_Gateway' ) ) {
	class WC_Noqodi_Payment_Gateway extends WC_Payment_Gateway {
		public function __construct() {
			$this->id = 'noqodi_payment';
			$this->icon = apply_filters('woocommerce_noqodi_icon', plugins_url('/assets/noqodi2.png', __FILE__));
			$this->has_fields = false;
			$this->method_title = _( 'noqodi Payment');
			$this->method_description = _('noqodi payment gateway system');
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->merchantCode = $this->get_option( 'merchantCode' );
            $this->noqodi_gateway_host = $this->get_option( 'noqodi_gateway_host' );
            $this->noqodi_api_host = $this->get_option( 'noqodi_api_host' );
            $this->authorizationCode = $this->get_option( 'authorizationCode' );
            $this->instructions = $this->get_option( 'instructions',
            $this->description );
			$this->init_form_fields();
			$this->init_settings();
			add_action('woocommerce_update_options_payment_gateways_' .$this->id, array( $this, 'process_admin_options') );

	       if ( ! $this->is_valid_for_use() ) {
            			$this->enabled = 'no';
            		}

           if ( 'yes' === $this->enabled ) {
           			add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'order_received_text' ), 10, 2 );
           		}
		}
		public function init_form_fields() {
			$this->form_fields = apply_filters('woo_noqodi_pay_fields', array(
			'enabled' => array(
			'title' => _( 'Enable/Disable'),
			'type' =>'checkbox',
			'label' => _( 'Enable or Disable noqodi payments'),
			'default' => 'no'
			),
			'title' => array(
            			'title' => _( 'noqodi Payments Gateway'),
            			'type' =>'text',
            			'default' => _('noqodi Payments Gateway'),
            			'desc_tip' =>true,
            			'description' => _('Add a new title for the noqodi Payments Gateway that customer will see when they are in the checkout page'),
            ),
           'description' => array(
                       			'title' => _( 'noqodi Payment Gateway Description'),
                       			'type' =>'textarea',
                       			'default' => _('Please remit your payment to the shop to allow for the delivery to be made'),
                       			'desc_tip' =>true,
                       			'description' => _('Add a new title for the Noqodi Payments Gateway that customer will see when they are in the checkout page'),
            ),
           'merchantCode' => array(
             					'title'         => __( 'merchant code'),
             					'type'          => 'text',
             					'description'   => __( 'Merchant Code, given by the Bank'),
             					'placeholder'   => __( 'Merchant Code'),
             					'desc_tip'      => true
           ),
          'noqodi_gateway_host' => array(
          					'title'         => __( 'noqodi gateway URL'),
          					'type'          => 'text',
          					'css'           => 'width:100%',
          					'description'   => __( 'noqodi URL'),
          					'placeholder'   => __( 'noqodi URL'),
          					'default'       => __( 'https://pay-dev02.noqodi.com/noqodi-payment'),
          					'desc_tip'      => true
          ),
          'noqodi_api_host' => array(
                    		'title'         => __( 'noqodi api URL'),
                    		'type'          => 'text',
                    		'css'           => 'width:100%',
                    		'description'   => __( 'noqodi URL'),
                    		'placeholder'   => __( 'noqodi URL'),
                    		'default'       => __( 'https://paymentapi-dev02.noqodi.com/payment-api/v2/payments'),
                    		'desc_tip'      => true
           ),
          'authorizationCode' => array(
                          	'title'         => __( 'authorization code'),
                          	'type'          => 'text',
                          	'description'   => __( 'Authorization Code, given by the Bank'),
                          	'placeholder'   => __( 'Authorization Code'),
                          	'desc_tip'      => true
          ),
          'debug'         => array(
                            'title'       => __( 'debug log', 'woocommerce' ),
                            'type'        => 'checkbox',
                            'label'       => __( 'Enable logging', 'woocommerce' ),
                            'default'     => 'no',
                       		'description' => sprintf( __( 'Log noqodi events, this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'woocommerce' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'noqodi' ) . '</code>' ),
          ),
          'instructions' => array(
                            'title' => _( 'Instructions'),
                            'type' =>'textarea',
                            'default' => _('Default instructions'),
                            'desc_tip' =>true,
                            'description' => _('Instructions that will be added to the thank you page and order email'),
          ),

		  ));
		}
		public function process_payment( $order_id)
		{
    	 $order = wc_get_order( $order_id );
         if ( ! $order ) {
         $order_id = wc_get_order_id_by_order_key( $order_key );
         $order    = wc_get_order( $order_id );
         }
         $response_json = $this->callPreAuth($order_id) ;
         foreach ($response_json as $key => $item) {
            foreach($item as $key => $name){
                if($key == 'status'){
                    $noqodi_preauth_response_status =$name;
                }
                elseif($key == 'errorCode')
                {
                    foreach($name as $key => $name4)
                    {
                        if($key == 'message'){
                        $noqodi_error_msg =$name4;
                        }
                    }
                }
                if($key == 'preAuthToken'){
                  $preAuthToken =$name;
             }
           }
         }
         if($noqodi_preauth_response_status=='FAILURE' || $preAuthToken == '')
         {
		    wc_add_notice( __( 'Payment error: Failed to communicate with noqodi server. '.$noqodi_error_msg, 'woo-noqodi' ), 'error' );
    		return array(
         	'result'	=> 'fail',
         	'redirect'	=> '',
             );
         }
        return array('result' => 'success','redirect' =>$this->noqodi_gateway_host.'?paymentRequestToken='.$preAuthToken.'&hosted=true');
   		}

        function CallPreAuth($order_id)
        {
         $order = wc_get_order( $order_id );

                           		if ( ! $order ) {
                           			$order_id = wc_get_order_id_by_order_key( $order_key );
                           			$order    = wc_get_order( $order_id );
                           		}
        $order_key =$order->get_order_key();
        $data_array =  array(
              "serviceType"        => "PRE_AUTH",
              "serviceMode" => "NORMAL",
              "merchantInfo"         => array(
                    "merchantCode"         => $this->merchantCode,
                    "merchantLandingURL"   => home_url('/').'checkout/order-received/'.$order_id.'/?key='.$order_key.'&utm_nooverride=',
                    "merchantRequestId"    => wp_generate_uuid4(),
                    "merchantOrderId"      => wp_generate_uuid4()
              ),
              "paymentInfo"                => array(
                     "amount"              => array(
                     "value"               => $order->get_total(),
                     "currency"        => get_woocommerce_currency()
              ),
              "serviceProvided"   => "true",
              "pricingInfo"       => array(
                     "paymentTypes"    => array(
                                     "CCD","ECA"
                ),
              ),
            )
        );
        $make_call = $this->callAPI('POST', $this->noqodi_api_host.'/preAuth', json_encode($data_array));
        $response = json_decode($make_call, true);
        $errors = $response['response']['errors'];
        $data = $response['response']['data'][0];
          return $response;
        }
        function callAPI($method, $url, $data){
           $curl = curl_init();
           curl_setopt($curl, CURLOPT_URL, $url);
           curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                     'Authorization: '.$this->authorizationCode,
                     'Content-Type: application/json',
                  ));
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
           $result = curl_exec($curl);
               curl_close($curl);
               return $result;
        }
        	public function order_received_text( $text, $order ) {
             $noqodi_auth_response = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['utm_nooverride'] ) ? '' : wc_clean( wp_unslash( $_GET['utm_nooverride'] ) ) );
             $noqodi_auth_response_updated='['.$noqodi_auth_response.']';
             $final_noqodi_auth_response = json_decode($noqodi_auth_response_updated, true);
           	  foreach ($final_noqodi_auth_response as $key => $item) {
                   foreach($item as $key => $name){
                     if($key == 'statusInfo'){
                     foreach($name as $key => $name1){
                            if($key == 'status'){
                                  $noqodi_auth_response_status =$name1;
                                    }
                            elseif($key == 'errorCode')
                            {
                                    foreach($name1 as $key => $name4)
                                    {
                                        if($key == 'message'){
                                          $noqodi_error_msg =$name4;
                                         }
                                    }
                             }
                           }
                         }
                       }
                     }

        	if($noqodi_auth_response_status=='SUCCESS'){
                 $order->set_status( apply_filters( 'woocommerce_payment_complete_order_status',  'completed', $order->get_id(), $order ) );
                 $msg="Thank you for your payment. Your transaction has been completed, and a receipt for your purchase has been emailed to you. Log into your Noqodi account to view transaction details.";
             } else {
                 $order->set_status( apply_filters( 'woocommerce_payment_complete_order_status',  'Failed', $order->get_id(), $order ) );
                 $msg="noqodi payment is unsuccessful. Error: ".$noqodi_error_msg;
               }
            $order->save();
            do_action( 'woocommerce_payment_complete', $order->get_id() );
             if ( $order && $this->id === $order->get_payment_method() ) {
     			return esc_html__( $msg, 'woocommerce' );
        		}
        		return $text;
        	}
        /**
            	 * Check if this gateway is available in the user's country based on currency.
            	 *
            	 * @return bool
            	 */
        function is_valid_for_use() {
                     return in_array(
               get_woocommerce_currency(),
               apply_filters(
                'woocommerce_noqodi_supported_currencies',
                array( 'AED' )
                ),true
           		);
          }
        /**
        	 * Admin Panel Options.
        	 * - Options for bits like 'title' and availability on a country-by-country basis.
        	 *
        	 * @since 1.0.0
        	 */
        	public function admin_options() {
        		if ( $this->is_valid_for_use() ) {
        			parent::admin_options();
        		} else {
        			?>
        			<div class="inline error">
        				<p>
        					<strong><?php esc_html_e( 'Gateway disabled', 'woocommerce' ); ?></strong>: <?php esc_html_e( 'noqodi does not support your store currency.', 'woocommerce' ); ?>
        				</p>
        			</div>
        			<?php
        		}
        	}
    	}

  }
}

add_filter( 'woocommerce_payment_gateways','add_to_woo_noqodi_payment_gateway');

function add_to_woo_noqodi_payment_gateway( $gateways ) {
	$gateways[] = 'WC_Noqodi_Payment_Gateway';
	return $gateways;

}

