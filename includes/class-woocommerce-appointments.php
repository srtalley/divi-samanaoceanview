<?php

namespace SamanaOceanview;

class SOV_WooCommerceAppointments {

    public function __construct() {

        // add_filter( 'woocommerce_add_to_cart_validation', 'one_cart_item_at_the_time', 10, 3 );

        add_action( 'template_redirect', array($this, 'empty_cart_redirection') );

        add_filter('woocommerce_billing_fields', array( $this, 'customize_billing_fields'), 10, 2);

        add_filter( 'woocommerce_add_to_cart_redirect', array($this, 'skip_cart_redirect_checkout') );
 

        add_action( 'woocommerce_init', array($this, 'change_woocommerce_text_strings'), 10, 1);
       
        add_filter ( 'woocommerce_cart_item_name', array($this, 'wc_checkout_modify_order'), 10, 3 );

        // add_filter ( 'woocommerce_cart_item_name', array($this, 'wc_checkout_modify_order_end'), 100, 3 );

        add_filter( 'woocommerce_get_item_data', array($this, 'checkout_time_in_cart_display'), 10, 2 );

        add_action( 'woocommerce_before_calculate_totals', array($this, 'update_appointment_meta'), 10, 1);


        add_action( 'woocommerce_after_appointment_form_output', array($this, 'add_checkin_checkout_dates'), 1, 2 );
        add_action( 'woocommerce_after_appointment_form_output', array($this, 'add_checkin_checkout_dates_end'), 100, 2 );
        // add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'hide_my_item_meta') );

        add_filter( 'woocommerce_order_item_permalink', array($this, 'remove_woocommerce_order_item_permalink') );

        // add_filter( 'wc_appointments_get_summary_list', array($this, 'modify_wc_appointments_get_summary_list'), 10, 1 );
        add_filter( 'woocommerce_order_item_get_formatted_meta_data', array($this, 'change_order_display_meta'), 10, 1 );

        // add_filter( 'woocommerce_appointments_time_slots_html', array($this, 'change_woocommerce_appointments_time_slots_html'), 10, 10 );
        add_filter( 'get_product_addons_fields', array($this, 'change_addon_field_labels'), 10, 2 );

        add_filter( 'woocommerce_product_addons_option_price', array($this, 'format_addon_price'), 10, 2 );
    }

    // public function change_woocommerce_appointments_time_slots_html($slots_html, $slots, $intervals, $time_to_check, $staff_id, $from, $to, $timezone, $thisme, $appointments) {
    // }

    
    // function hide_my_item_meta( $hidden_meta ) {

    //     $hidden_meta[] = 'staff';
    //     return $hidden_meta;
    // }
    /**
     * add a checkin checkout display
     */
    public function add_checkin_checkout_dates($position = 'after', $product_id = 0) {
        $html = '<div class="sov-start-addon-fields">';
        echo $html;

        if($product_id != 0) {
            // get the product
            $product = wc_get_product($product_id);
            $duration_unit = $product->get_duration_unit();

            $html = '';
            if($duration_unit == 'day') {
                $start_label = 'Check In:';
                $end_label = 'Check Out:';
                $type = 'type-reservation';

                $html .= '<div class="wc-appt-checkin-checkout-dates ' . $type . '">';
                $html .= '<div class="wc-appt-checkin-date"><span class="sov-wc-appt-label checkin-label">' . $start_label . ' </span> <span class="checkin-value"></span></div>';
                $html .= '<div class="wc-appt-checkout-date"><span class="sov-wc-appt-label checkout-label">' . $end_label . ' </span> <span class="checkout-value"></span></div>';
                $html .= '</div>';
            }

            echo $html;
        } 
    }
    public function add_checkin_checkout_dates_end($position = 'after', $product_id = 0) {
        
        echo '</div>';

    }
    /**
     * Only allow one cart item at a time
     */
    // public function one_cart_item_at_the_time( $passed, $product_id, $quantity ) {
    //     if( ! WC()->cart->is_empty())
    //         WC()->cart->empty_cart();
    //     return $passed;
    // }

    /**
     * Redirect to the owners portal calendar page
     */
    function empty_cart_redirection(){
        if(function_exists('is_cart')) {
            if( is_cart() ) :
        
                // Here set the Url redirection
                $url_redirection = site_url('/availability/');
                
                // When trying to access cart page if cart is already empty  
                if( WC()->cart->is_empty() ){
                    wp_safe_redirect( $url_redirection );
                    exit();
                }
                
                // When emptying cart on cart page
                wc_enqueue_js( "jQuery(function($){
                    $(document.body).on( 'wc_cart_emptied', function(){
                        if ( $( '.woocommerce-cart-form' ).length === 0 ) {
                            $(window.location).attr('href', '" . $url_redirection . "');
                            return;
                        }
                    });
                });" );
                endif;
        }
      
    }
    /**
     * Add the data from the appointable form fields to the 
     * checkout fields
     */
    public function customize_billing_fields( $address_fields ) {
        // $cart = WC()->cart->cart_contents;

        // $billing_field_values = array(
        //     'first_name' => '',
        //     'last_name' => '',
        //     'email' => '',
        //     'phone' => ''
        // );
        // foreach($cart as $item) {
        //     if(isset($item['appointment']) && isset($item['addons'])) {
        //         foreach($item['addons'] as $field) {
        //             if($field['name'] == 'First Name') {
        //                 $billing_field_values['first_name'] = $field['value'];
        //             }
        //             if($field['name'] == 'Last Name') {
        //                 $billing_field_values['last_name'] = $field['value'];
        //             }
        //             if($field['name'] == 'Email Address') {
        //                 $billing_field_values['email'] = $field['value'];
        //             }
        //             if($field['name'] == 'Phone Number') {
        //                 $billing_field_values['email'] = $field['value'];
        //             }
        //         }
        //         break;
        //     }
        // }
        // $address_fields['billing_first_name']['default'] = $billing_field_values['first_name'];
        // $address_fields['billing_last_name']['default'] = $billing_field_values['last_name'];
        // $address_fields['billing_email']['default'] = $billing_field_values['email'];        $address_fields['billing_phone']['default'] = $billing_field_values['phone'];

        unset($address_fields['billing_company']);
        unset($address_fields['billing_country']);
        unset($address_fields['billing_address_1']);
        unset($address_fields['billing_address_2']);
        unset($address_fields['billing_city']);
        unset($address_fields['billing_city']);
        unset($address_fields['billing_state']);
        unset($address_fields['billing_postcode']);
        // unset($address_fields['billing_phone']);

        return $address_fields;
    }

    /** 
     * Redirect to checkout after add to cart
     */
    public function skip_cart_redirect_checkout( $url ) {
        // maybe add checks to see what kind of product is being added
        return wc_get_checkout_url();
    }

    public function change_woocommerce_text_strings () {
        add_filter( 'woocommerce_thankyou_order_received_text', array($this, 'change_woocommerce_thankyou_order_received_text') );
        add_filter( 'gettext', array($this,'change_woocommerce_gettext_strings'), 20, 3 );
        // remove the notice from the checkout 
        add_filter( 'wc_add_to_cart_message_html', '__return_false' );
    } 

    public function change_woocommerce_thankyou_order_received_text() {
        // return 'Thank you for reaching out to us. We will be contacting you soon.';
         return '';
    }

     /**
    * Change WooCommerce Strings
    *
    * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
    */
    function change_woocommerce_gettext_strings( $translated_text, $text, $domain ) {


        if($domain == 'woocommerce') {
            switch ( $translated_text ) {

                // Switching these headings but they need to be slightly different
                // or it enters a loop where it keeps trying to replace the other
                // one over and over.
                case 'Cost':
                    $translated_text = __( 'Standard Pricing:', 'woocommerce' );
                    break;
                case 'Order number:':
                    $translated_text = __( 'Request number', 'woocommerce' );
                    break;
                case 'Order details':
                    $translated_text = __( 'Request Details', 'woocommerce' );
                    break;
                case 'Billing address':
                    $translated_text = __( 'Contact Info', 'woocommerce' );
                    break;
                case 'Billing details':
                    $translated_text = __( 'Contact Info', 'woocommerce' );
                    break;
                case 'Your order':
                    $translated_text = __( 'Your Request', 'woocommerce');
                    break;
                case 'Order notes':
                    $translated_text = __( 'Notes', 'woocommerce');
                    break;
                case 'Notes about your order, e.g. special notes for delivery.':
                    $translated_text = __( 'Any additional notes or information', 'woocommerce');
                    break;
                
            }
        } 
        if($domain == 'woocommerce-appointments') {
            switch ( $translated_text ) {
                case 'Appointment':
                    $translated_text = __( 'Next', 'woocommerce-appointments');
                    break;
                case 'Check Availability':
                    $translated_text = __( 'Next', 'woocommerce-appointments');
                    break;
                case 'Request Confirmation':
                    $translated_text = __( 'Click to Submit Your Request', 'woocommerce-appointments');
                    break;
                case 'Check appointment availability':
                    $translated_text = __( 'Pay Later', 'woocommerce-appointments');
                    break;
                case 'Your appointment is awaiting confirmation. You will be notified by email as soon as we\'ve confirmed availability.':
                    $translated_text = __( '', 'woocommerce-appointments');
                    break;
                case 'Scheduled Product':
                    $translated_text = __( 'Reserved Item', 'woocommerce-appointments');
                    break;
                case 'Appointment Date':
                    $translated_text = __( 'Scheduled Date', 'woocommerce-appointments');
                    break;
                case 'Appointment ID':
                    $translated_text = __( 'Reservation ID', 'woocommerce-appointments');
                    break;
                case 'Appointment Duration':
                    $translated_text = __( 'Duration', 'woocommerce-appointments');
                    break;
                case 'You can view and edit this appointment in the dashboard here: %s':
                    $translated_text = __( 'You can view and edit this reservation in the dashboard here: %s', 'woocommerce-appointments');
                    break;
                case 'This appointment is awaiting your approval. Please check it and inform the customer if the date is available or not.':
                    $translated_text = __( 'You can approve or deny the request here:', 'woocommerce-appointments');
                    break;
                case 'An appointment has been made by %s and is awaiting your approval. The details of this appointment are shown below.':
                    $translated_text = __( 'A request to reserve a stay has been made by %s. The details and contact information are below.', 'woocommerce-appointments');
                    break;
                case 'An new appointment has been made by %s. The details of this appointment are shown below.':
                    $translated_text = __( 'A reservation has been made by %s. The details and contact information are below.', 'woocommerce-appointments');
                    break;
            }
        } 
        


        return $translated_text;
    } // end function 


    /**
     * update the cart meta with the checkout date
     * or the start / end times
     */

    public function update_appointment_meta() {

        // see if the meta is not set
        $cart = WC()->cart->cart_contents;
        foreach( $cart as $cart_item_id => $cart_item ) {
            if(isset($cart_item['appointment'])) {

                // get the product
                $product_id = $cart_item['product_id'];
                $product = wc_get_product($product_id);
                $duration_unit = $product->get_duration_unit();
                
                if($duration_unit == 'day') {
                    // calculate checkout date
                    $checkin_date = \DateTime::createFromFormat('Y-m-d', $cart_item['appointment']['_date']);
                    $cart_item['amr_checkin_date'] = $checkin_date->format('F d, Y');
                    $checkout_date = $checkin_date;
                    $checkout_date = $checkout_date->modify('+' . ($cart_item['appointment']['_duration']-1) . ' day');
                    $cart_item['amr_checkout_date'] = $checkout_date->format('F d, Y');
                }
                if($duration_unit == 'hour') {
                    // calculate start time and end time
                    $start_datetime = \DateTime::createFromFormat('Y-m-d G:i', $cart_item['appointment']['_date'] . ' ' . $cart_item['appointment']['_time']);
                    $cart_item['amr_appt_date'] = $start_datetime->format('F d, Y');
                    $cart_item['amr_starttime'] = $start_datetime->format('g:i A');

                    $end_datetime = $start_datetime;
                    $end_datetime = $end_datetime->modify('+' . $cart_item['appointment']['_duration'] . ' minute');
                    $cart_item['amr_endtime'] = $end_datetime->format('g:i A');
                }

            }

            WC()->cart->cart_contents[$cart_item_id] = $cart_item;
        }
        WC()->cart->set_session();

    }
    /**
    * Add the ability to delete products and change the quantity in the 
    * review section on checkout
    */
    public function wc_checkout_modify_order( $product_title, $cart_item, $cart_item_key ) {
        /* Checkout page check */
        if (  is_checkout() ) {
            /* Get Cart of the user */
            $cart = WC()->cart->get_cart();
                foreach ( $cart as $cart_key => $cart_value ){
                    if ( $cart_key == $cart_item_key ){
                        $product_id = $cart_item['product_id'];
                        $_product   = $cart_item['data'] ;
                        $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

                        $return_value = '<div class="product-name-left">';

                        /* Step 1 : Add delete icon */
                        // $return_value = sprintf(
                        //     '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                        //     esc_url( WC()->cart->get_remove_url( $cart_key ) ),
                        //     __( 'Remove this item', 'woocommerce' ),
                        //     esc_attr( $product_id ),
                        //     esc_attr( $_product->get_sku() )
                        // );
                        
                        /* Step 2 : Add product thumb */
                        // $thumbnail = $_product->get_the_post_thumbnail_url('full');
                        $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $_product->get_id() ), 'single-post-thumbnail' );
                        if(is_array($thumbnail_src)) {
                            $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $_product->get_id() ), 'single-post-thumbnail' )[0];
                        } else {
                            $thumbnail = '';
                        }

                        $return_value .= '<div class="wc-checkout-thumbnail" style="background-image:url(' . $thumbnail . ');">';
                        // $return_value .= '<a href="' . esc_url( $product_permalink ) . '">' . $thumbnail . '</a>'; 
                        $return_value .= '<div class="product_name" >' . $product_title . '</div>' ;

                        $return_value .= '</div>';

                        /* Step 3 : Add product name */

                        $return_value .= sprintf(
                            '<div class="product-name-middle"><a href="%s" class="remove-item" title="%s" data-product_id="%s" data-product_sku="%s">&times; Click to remove this request and start over</a></div>',
                            esc_url( WC()->cart->get_remove_url( $cart_key ) ),
                            __( 'Remove this item', 'woocommerce' ),
                            esc_attr( $product_id ),
                            esc_attr( $_product->get_sku() )
                        );
                        $return_value .= '<div class="product-name-bottom"><div class="checkout-details-header">Request Details</div>';

                        /* Step 4 : Add Price ea. */
                        // $return_value .= '<span class="price-each"><strong>Price each:</strong> ' . WC()->cart->get_product_price( $_product ) . '</span>';
                        /* Step 3 : Add quantity selector */
                        // moved to separate function
                        // if ( $_product->is_sold_individually() ) {
                        //     $return_value .= sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_key );
                        // } else {
                        //     $return_value .= woocommerce_quantity_input( array(
                        //         'input_name'  => "cart[{$cart_key}][qty]",
                        //         'input_value' => $cart_item['quantity'],
                        //         'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                        //         'min_value'   => '1'
                        //         ), $_product, false );
                        // }
                        return $return_value;
                    }
                }
        }else{
            /*
            * It will return the product name on the cart page.
            * As the filter used on checkout and cart are same.
            */
            $_product   = $cart_item['data'] ;
            $product_permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
            if ( ! $product_permalink ) {
                $return_value = $_product->get_title() . '&nbsp;';
            } else {
                $return_value = sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title());
            }
            return $return_value;
        }
    } // end function 

    /**
     * Close the above html
     */
    public function wc_checkout_modify_order_end() {
        if(is_checkout()) {
            return '</span>';
        }
    }
    /**
     * Add the checkin/checkout time or the time in / time out
     */
    public function checkout_time_in_cart_display( $item_data, $cart_item ) {
        $checkin_date = isset($cart_item['amr_checkin_date']) ? $cart_item['amr_checkin_date']: '';
        $checkout_date = isset($cart_item['amr_checkout_date']) ? $cart_item['amr_checkout_date']: '';
        if ( ! empty( $checkout_date ) ) {
            // if($cart_item['product_id'] == 211976) {
                $item_data[] = array(
                    'name' => __('Check In', 'woocommerce'),
                    'value' => '3 - 4 PM, ' . $checkin_date,
                );
            // } else {
            //     $item_data[] = array(
            //         'name' => __('Start Date', 'woocommerce'),
            //         'value' => $checkin_date,
            //     );
            // }

        }
        if ( ! empty( $checkout_date ) ) {
            // if($cart_item['product_id'] == 211976) {
                // $date = dae_create($checkout_date);
                $tomorrow = date('F j, Y',strtotime($checkout_date . "+1 days"));
                $item_data[] = array(
                    'name' => __('Check Out', 'woocommerce'),
                    'value' => '11 - 12 PM, ' . $tomorrow,
                );
            // } else {
            //     $item_data[] = array(
            //         'name' => __('End Date', 'woocommerce'),
            //         'value' => $checkout_date,
            //     );
            // }
        }
        
        $appt_date = isset($cart_item['amr_appt_date']) ? $cart_item['amr_appt_date']: '';
        $start_datetime = isset($cart_item['amr_starttime']) ? $cart_item['amr_starttime']: '';
        $end_datetime = isset($cart_item['amr_endtime']) ? $cart_item['amr_endtime']: '';

        if ( ! empty( $appt_date ) ) {
            $item_data[] = array(
                'name' => __('Date Scheduled', 'woocommerce'),
                'value' => $appt_date,
            );
        }
        if ( ! empty( $start_datetime ) ) {
            $item_data[] = array(
                'name' => __('Start Time', 'woocommerce'),
                'value' => $start_datetime,
            );
        }
        if ( ! empty( $end_datetime ) ) {
            $item_data[] = array(
                'name' => __('End Time', 'woocommerce'),
                'value' => $end_datetime,
            );
        }

        return $item_data;
    }
    /**
     * Remove the link to the products on the order page
     */
    public function remove_woocommerce_order_item_permalink($link) {
       return false;
    }

    /**
     * Modify the summary list
     */
    // public function modify_wc_appointments_get_summary_list($summary) {
    //     return $summary;
    // }

    /**
     * Change the items displayed in the meta
     */
    public function change_order_display_meta($formatted_meta){
        $temp_metas = [];
        foreach($formatted_meta as $key => $meta) {
            if ( isset( $meta->key ) ) {
                if($meta->key != 'Days Reserved') {
                    $temp_metas[$key] = $meta;
                }
            }
        }
        return $temp_metas;
    }
    /**
     * Change labels for addons, adding HTML as needed
     */
    public function change_addon_field_labels($product_addons, $post_id) {
        foreach($product_addons as $key => $product_addon) {
            if($product_addon['name'] == 'Do you have a fishing license? Fishing license is required (open link to purchase)') {
                $product_addon['name'] = 'Do you have a fishing license? Fishing license is required (<a href="https://www.cpwshop.com/licensing.page?&e4q=e671b5c6-941e-4be5-8473-8d46e16e79c4&e4p=139958f9-9196-4397-9917-b993bb9fab51&e4ts=1651706512&e4c=aspira&e4e=snasoco01&e4rt=Safetynet&e4h=b135c2b59ca60612abfc0485201b17a3" target="_blank">open link to purchase</a>)';
                $product_addons[$key] = $product_addon;
            }

        }
        return $product_addons;
    }
    public function format_addon_price($price_html, $label_info) {
        $new_price_html = str_replace('(+', '&#8211;&nbsp;', $price_html);
        $new_price_html = str_replace(')', '', $new_price_html);
        return $new_price_html;
        // $price = '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span>' . $label_info['price'] . '</bdi></span>';
    }
} // end class SOV_WooCommerceAppointments

$sov_woocommerce_appointments = new SOV_WooCommerceAppointments();
