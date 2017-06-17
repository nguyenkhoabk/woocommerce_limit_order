<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_LIMIT_ORDER' ) )
{
    /**
     * This class adds time picker for delivery when shipping
     * Delivery time will be save as a part of order and will be included in emails
     */
    class WLO_LIMIT_ORDER
    {
        /**
         * Class constructor
         * Add hooks to Woocommerce
         */
        function __construct()
        {
            // Add or update order log
            add_action( 'woocommerce_checkout_order_processed', 'WLO_LIMIT_CUSTOMER::check_order_log', 10, 2 );
            // Limit order by customer
//            add_action( 'woocommerce_before_checkout_form', 'WLO_LIMIT_CUSTOMER::check_valid_order_time', 10 );
            add_action( 'woocommerce_checkout_process', 'WLO_LIMIT_CUSTOMER::check_valid_order_time', 10 );
            // Limit product category
            add_action( 'woocommerce_checkout_process', 'WLO_LIMIT_PRODUCT_CATEGORY::limit_product_category', 20 );
            // Limit amount product
            add_action( 'woocommerce_checkout_process', 'WLO_Limit_Product::limit_amount_product', 15 );
            // Limit Quantity product
            add_action( 'woocommerce_checkout_process', 'WLO_Limit_Product::limit_qty_product', 20 );
            add_action( 'woocommerce_checkout_process', 'WLO_Limit_Product::limit_total_qty', 25 );
//            add_action( 'woocommerce_before_checkout_form', 'WLO_LIMIT_PRODUCT_CATEGORY::limit_product_category', 10 );
        }   

    }
}