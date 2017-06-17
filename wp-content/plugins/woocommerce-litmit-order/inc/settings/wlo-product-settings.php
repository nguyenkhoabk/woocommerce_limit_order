<?php
/**
 * Product Settings
 * Functions get setting datas in Product tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_Product_Settings' ) )
{
    /**
     * Class WLO_Product_Settings
     */
    class WLO_Product_Settings
    {
        /**
         * Return products settings
         * @return $product_settings array general settings
         */
        public static function get_settings()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            $product_settings['limit_on_product'] = isset( $wlo_product_options['limit_on_product'] ) ? $wlo_product_options['limit_on_product'] : '';
            $product_settings['enable_limit_amount_product'] = isset( $wlo_product_options['enable_limit_amount_product'] ) ? $wlo_product_options['enable_limit_amount_product'] : '';
            $product_settings['max_amount'] = isset( $wlo_product_options['max_amount'] ) ? $wlo_product_options['max_amount'] : 5;
            $product_settings['min_amount'] = isset( $wlo_product_options['min_amount'] ) ? $wlo_product_options['min_amount'] : 2 ;
            $product_settings['enable_limit_qty_product'] = isset( $wlo_product_options['enable_limit_qty_product'] ) ? $wlo_product_options['enable_limit_qty_product'] : '';
            $product_settings['limit_qty_product_types'] = isset( $wlo_product_options['limit_qty_product_types'] ) ? $wlo_product_options['limit_qty_product_types'] : 'all_products';
            $product_settings['global_product_quantity_max'] = isset( $wlo_product_options['global_product_quantity_max'] ) ? $wlo_product_options['global_product_quantity_max'] : 10;
            $product_settings['global_product_quantity_min'] = isset( $wlo_product_options['global_product_quantity_min'] ) ? $wlo_product_options['global_product_quantity_min'] : 2;
            $product_settings['max_amount_message'] = $wlo_product_options['max_amount_message'] === NULL ? __( 'There are [total_cart_items] in cart, exceeded maximum amount product can order is [max_amount]', 'wlo' ) : $wlo_product_options['max_amount_message'];
            $product_settings['min_amount_message'] = $wlo_product_options['min_amount_message'] === NULL ? __( 'There are [total_cart_items] in cart, not reach minimum amount product must order is [min_amount]', 'wlo' ) : $wlo_product_options['min_amount_message'];;
            $product_settings['max_qty_message'] = $wlo_product_options['max_qty_message'] === NULL ? __( 'The product [product_name] placed [product_qty] and exceeded maximum quantity a product can buy is [max_qty]', 'wlo' ) : $wlo_product_options['max_qty_message'];;
            $product_settings['min_qty_message'] = $wlo_product_options['min_qty_message'] === NULL ? __( 'The product [product_name] placed [product_qty] and not reach minimum quantity a product must buy is [min_qty]', 'wlo' ) : $wlo_product_options['min_qty_message'];;
            // limit total amount product options
            $product_settings['enable_limit_product_purchase'] = isset( $wlo_product_options['enable_limit_product_purchase'] ) ? $wlo_product_options['enable_limit_product_purchase'] : '';
            $product_settings['limit_total_qty_product_types'] = isset( $wlo_product_options['limit_total_qty_product_types'] ) ? $wlo_product_options['limit_total_qty_product_types'] : 'all_products';
            $product_settings['global_total_qty_product'] = NULL === $wlo_product_options['global_total_qty_product'] ? 10 : $wlo_product_options['global_total_qty_product'];
            $product_settings['total_qty_message'] = $wlo_product_options['total_qty_message'] === NULL ? __( 'The [product_name] purchased total quantity [total_qty_purchased] across all orders from [from_date_range] to [to_date_range], you just can buy [total_qty] so you can not buy [current_qty] in this order.', 'wlo' ) : $wlo_product_options['total_qty_message'];
            return $product_settings;
        }
    }
}