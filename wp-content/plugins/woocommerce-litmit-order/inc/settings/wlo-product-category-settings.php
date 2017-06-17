<?php
/**
 * Product Category Settings
 * Functions get setting datas in Product tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_Product_Category_Settings' ) )
{
    /**
     * Class WLO_Product_Category_Settings
     */
    class WLO_Product_Category_Settings
    {
        /**
         * Return products settings
         * @return $product_settings array general settings
         */
        public static function get_settings()
        {
            $wlo_product_category_options = get_option( WLO_PRODUCT_CATEGORY_OPTION );
            return $wlo_product_category_options;
        }
    }
}