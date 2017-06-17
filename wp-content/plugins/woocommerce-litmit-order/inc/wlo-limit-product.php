<?php
/**
 * Limit Product
 * Functions limit order product
 */
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_Limit_Product' ) )
{
    /**
     * Class WLO_Limit_Product
     */
    class WLO_Limit_Product
    {

        /**
         * Function check current date with options in general setting
         * @param $general_settings array general settings
         * @return bool result after checked with options in general setting
         */
        static function check_limit_date_and_time( $general_settings )
        {
            // check limit date and range time limit
            if( WLO_General_Settings::is_limit_day() )
            {
                $current_hour = date( 'H' );
                $from_time = 0;
                $to_time = 24;
                if ( WLO_General_Settings::enable_limit_range_time() )
                {
                    $advance_time_selected_arr = explode( '-', $general_settings['advance_time_selected'] );
                    $from_time = $advance_time_selected_arr[0];
                    $to_time = $advance_time_selected_arr[1];
                }
                if( intval( $current_hour ) >= intval( $from_time ) && intval( $current_hour ) <= intval( $to_time ) )
                {
                    return true;
                }
            }
            return false;
        }

        /**
         * Functions check current time have to limit order
         * @param $wlo_product_options array product settings
         * @param $general_settings array general settings
         * @return bool result after checked current time heck current time have to limit order
         */
        static function is_limit_product( $wlo_product_options, $general_settings )
        {
            if( self::check_limit_date_and_time( $general_settings ) )
            {
                // if enable limit product
                if( 'yes' == $wlo_product_options['limit_on_product'] )
                {
                    return true;
                }
            }
            return false;
        }
        
        /**
         * Check amount product in cart
         */
        public static function limit_amount_product()
        {
            $wlo_product_options = WLO_Product_Settings::get_settings();
            $general_settings = WLO_General_Settings::get_settings();
            // check limit date and range time limit, if true execute the rules on limit product tab
            if( self::is_limit_product($wlo_product_options, $general_settings) )
            {
                // if enable limit amount product
                if( 'yes' == $wlo_product_options['enable_limit_amount_product'] )
                {
                    $max_amount_product = $wlo_product_options['max_amount'];
                    $min_amount_product = $wlo_product_options['min_amount'];
                    // get total items in cart
                    $total_items = count( WC()->cart->get_cart() );
                    if( ( ! empty( $max_amount_product ) && 0 != $max_amount_product ) )
                    {
                        if ( $total_items > $max_amount_product )
                        {
                            $max_amount_message = $wlo_product_options['max_amount_message'];
                            $max_amount_message = str_replace( '[max_amount]' , $max_amount_product, $max_amount_message );
                            $max_amount_message = str_replace( '[total_cart_items]', $total_items , $max_amount_message );

                            // add target page to message
                            $max_amount_message = WLO_General_Settings::add_target_link( $max_amount_message );

                            wc_add_notice( $max_amount_message , 'error' );

                        }
                    }
                    if( ( ! empty( $min_amount_product ) && 0 != $min_amount_product ) )
                    {
                        if ( $total_items < $min_amount_product )
                        {
                            $min_amount_message = $wlo_product_options['min_amount_message'];
                            $min_amount_message = str_replace( '[min_amount]' , $min_amount_product, $min_amount_message );
                            $min_amount_message = str_replace( '[total_cart_items]', $total_items , $min_amount_message );
                            // add target page to message
                            $min_amount_message = WLO_General_Settings::add_target_link( $min_amount_message );

                            wc_add_notice( $min_amount_message , 'error' );

                        }
                    }
                }
            }
        }

        /**
         * Check quantity product on order
         */
        public static function limit_qty_product()
        {
            $wlo_product_options = WLO_Product_Settings::get_settings();
            $general_settings = WLO_General_Settings::get_settings();
            // check limit date and range time limit, if true execute the rules on limit product tab
            if( self::is_limit_product($wlo_product_options, $general_settings) )
            {
                // if enable limit quantity product
                if( 'yes' == $wlo_product_options['enable_limit_qty_product'] )
                {
                    $cart_items = WC()->cart->get_cart();
                    // if enable apply to all products
                    if( 'all_products' == $wlo_product_options['limit_qty_product_types'] )
                    {
                        $max_qty = $wlo_product_options['global_product_quantity_max'];
                        $min_qty = $wlo_product_options['global_product_quantity_min'];
                    }
                    foreach ( $cart_items as $item )
                    {
                        $product_id = $item['product_id'];
                        $product_qty = $item['quantity'];
                        $product_name = $item['data']->post->post_title;
                        // check limit qty on specific product
                        $is_limit_qty = get_post_meta( $product_id, 'wlo_enable_limit_qty', true );
                        if( 'yes' == $is_limit_qty )
                        {
                            $min_qty =  get_post_meta( $product_id, 'wlo_min_qty', true );
                            $max_qty =  get_post_meta( $product_id, 'wlo_max_qty', true );
                        }
                        // check quantity product in cart
                        if ( ! empty( $max_qty ) && 0 != $max_qty )
                        {
                            // if quantity product exceed maximum quantity product
                            if ( $product_qty > $max_qty )
                            {
                                $max_qty_message = $wlo_product_options['max_qty_message'];
                                $max_qty_message = str_replace( '[product_name]' , $product_name, $max_qty_message );
                                $max_qty_message = str_replace( '[product_qty]' , $product_qty, $max_qty_message );
                                $max_qty_message = str_replace( '[max_qty]' , $max_qty, $max_qty_message );
                                // add target page to message
                                $max_qty_message = WLO_General_Settings::add_target_link( $max_qty_message );

                                wc_add_notice( $max_qty_message , 'error' );
                            }
                        }
                        // check quantity product in cart
                        if ( ! empty( $min_qty ) && 0 != $min_qty )
                        {
                            // if quantity product not reach minimum quantity product
                            if ( $product_qty < $min_qty )
                            {
                                $min_qty_message = $wlo_product_options['min_qty_message'];
                                $min_qty_message = str_replace( '[product_name]' , $product_name, $min_qty_message );
                                $min_qty_message = str_replace( '[product_qty]' , $product_qty, $min_qty_message );
                                $min_qty_message = str_replace( '[min_qty]' , $min_qty, $min_qty_message );
                                // add target page to message
                                $min_qty_message = WLO_General_Settings::add_target_link( $min_qty_message );

                                wc_add_notice( $min_qty_message , 'error' );
                            }
                        }
                    }
                }
            }
        }

        /**
         * Check total quantity a product can purchase cross all orders
         */
        public static function limit_total_qty()
        {
            $product_settings = WLO_Product_Settings::get_settings();
            // if enable option limit total quantity of product cross orders
            if ( 'yes' == $product_settings['enable_limit_product_purchase'] )
            {

                $date_range_information = WLO_General_Settings::is_belong_date_range();
                $cus_id = get_current_user_id();
                $list_products = array();
                $orders = array();
                // filter array, default check in today
                $args = array(
                    'start_date'    =>  date( 'Y-m-d' ),
                    'to_date'       =>  date( 'Y-m-d' ),
                );
                if ( 'yes' == $date_range_information['limit_range_date'] )
                {
                    // get customer's orders in date range
                    $args['start_date'] = $date_range_information['start_date'];
                    $args['to_date'] = $date_range_information['to_date'];
                    // get customer's order belong date range
                    $orders = WLO_Order_Settings::get_orders_by_customer_id( $cus_id, $args );
                }


                // get total amount of a product purchased
                foreach ( $orders as $order )
                {
                    $order_id = $order->ID;
                    $order_obj = new WC_Order( $order_id );
                    $order_items = $order_obj->get_items();
                    foreach ( $order_items as $order_item )
                    {
                        $list_products[$order_item['product_id']]['qty'] += $order_item['qty'];
                    }
                }
                // get total items in cart
                $cart_items = WC()->cart->get_cart();
                // check products in cart does not exceed the total amount of product can buy across all orders
                foreach ( $cart_items as $item )
                {
                    $global_total_amount_message = $product_settings['total_qty_message'];
                    $global_total_amount_val = $product_settings['global_total_qty_product'];
                    $cart_item_id = $item['product_id'];
                    $cart_item_qty = 0;
                    $cart_item_name = $item['data']->post->post_title;
                    $cart_limit_total_amount = $global_total_amount_val;
                    $cart_item_total_qty_purchased = 0;
                    // check if product has specific date range
                    $enable_date_range = get_post_meta( $cart_item_id, 'wlo_enable_limit_in_date_range', true );
                    if ( isset( $enable_date_range ) && 'yes' == $enable_date_range )
                    {
                        $product_date_range = get_post_meta( $cart_item_id, 'wlo_date_range_limit', true );
                        $is_belong_date_range = self::check_belong_date_range( $product_date_range );
                        if ( isset( $is_belong_date_range['limit_range_date'] ) && 'yes' == $is_belong_date_range['limit_range_date'] )
                        {
                            $args['start_date'] = $is_belong_date_range['start_date'];
                            $args['to_date'] = $is_belong_date_range['to_date'];
                            // total amount this item purchased
                            $cart_item_qty = $item['quantity'];
                            // get customer's order belong date range
                            $orders = WLO_Order_Settings::get_orders_by_customer_id( $cus_id, $args );
                            // get total amount of a product purchased

                            foreach ( $orders as $order )
                            {
                                $order_id = $order->ID;
                                $order_obj = new WC_Order( $order_id );
                                $order_items = $order_obj->get_items();
                                foreach ( $order_items as $order_item )
                                {


                                    if ( $cart_item_id == $order_item['product_id'] )
                                    {
                                        $cart_item_total_qty_purchased += $order_item['qty'];
                                    }
                                }

                            }
                            $cart_item_qty += $cart_item_total_qty_purchased;
                        }
                    }
                    else
                    {
                        // check current item does not exceed limit total amount
                        if( isset( $list_products[ $cart_item_id ] ) && ! is_null( $list_products[ $cart_item_id ] ) )
                        {
                            $cart_item_total_qty_purchased = $list_products[$cart_item_id]['qty'];
                            // total amount this item purchased
                            $cart_item_qty = $item['quantity'];
                            $cart_item_qty += $cart_item_total_qty_purchased;
                        }
                    }

                    // if product has specific total limit amount
                    $is_limit_total_qty = get_post_meta( $cart_item_id, 'wlo_enable_limit_total_qty', true );
                    if( isset( $is_limit_total_qty ) && 'yes' == $is_limit_total_qty )
                    {
                        // update limit total amount value
                        $cart_limit_total_amount =  get_post_meta( $cart_item_id, 'wlo_total_qty', true );
                    }

                    // check limit total amount

                    if ( $cart_item_qty > $cart_limit_total_amount )
                    {
                        $global_total_amount_message = str_replace( '[product_name]' , $cart_item_name, $global_total_amount_message );
                        $global_total_amount_message = str_replace( '[total_qty_purchased]' , $cart_item_total_qty_purchased, $global_total_amount_message );
                        $global_total_amount_message = str_replace( '[total_qty]' , $cart_limit_total_amount, $global_total_amount_message );
                        $global_total_amount_message = str_replace( '[current_qty]' , $item['quantity'], $global_total_amount_message );
                        $global_total_amount_message = str_replace( '[from_date_range]' , date( 'm/d/Y', strtotime( $args['start_date'] ) ), $global_total_amount_message );
                        $global_total_amount_message = str_replace( '[to_date_range]' , date( 'm/d/Y', strtotime( $args['to_date'] ) ), $global_total_amount_message );
                        // add target page to message
                        $global_total_amount_message = WLO_General_Settings::add_target_link( $global_total_amount_message );

                        wc_add_notice( $global_total_amount_message , 'error' );
                    }
                }
            }
        }

        /**
         * Check today belong limit dates range
         * @param $product_date_range string poduct date ranges
         * @return $result $args array date range information
         */
        public static function check_belong_date_range( $product_date_range )
        {
            // date range information
            $args = array(
                'limit_range_date'  =>  'no',
            );
            $current_date = date("m/d/Y");
            $date_range_arr = explode( "\n", $product_date_range );
            // if current date belong the range time, exceed limit by specific date
            foreach ( $date_range_arr as $range )
            {
                // if it is valid date range
                if( false !== strpos( $range, '-' ) )
                {
                    $range_arr = explode( '-' , $range );
                    $start_date = trim( $range_arr[0] );
                    $to_date = trim( $range_arr[1] );
                    if ( ( strtotime( $start_date ) <= strtotime( $current_date ) ) && ( strtotime( $to_date ) >= strtotime( $current_date ) ) )
                    {
                        $args['limit_range_date'] = 'yes';
                        $args['start_date'] = date( 'Y/m/d', strtotime( $start_date ) );
                        $args['to_date'] = date( 'Y/m/d', strtotime( $to_date ) );
                        break; // exit when match first date range
                    }
                }
            }

            return $args;
        }
    }
}