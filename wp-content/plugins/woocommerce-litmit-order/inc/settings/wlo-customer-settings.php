<?php
/**
 * Customer Settings
 * Functions get setting datas in Customer tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_Customer_Settings' ) )
{
    /**
     * Class WLO_Customer_Settings
     */
    class WLO_Customer_Settings
    {
        /**
         * Return customers settings
         * @return $customer_settings array general settings
         */
        public static function get_settings()
        {
            $wlo_user_options = get_option( WLO_USER_OPION );
            $customer_settings['limit_on_identity'] = isset( $wlo_user_options['limit_on_identity'] ) ? $wlo_user_options['limit_on_identity'] : '';
            $customer_settings['limit_time_for_customer'] = $wlo_user_options['limit_time_for_customer'] == NULL ? 5 : $wlo_user_options['limit_time_for_customer'];
            $customer_settings['message'] = $wlo_user_options['message'] == NULL ? __( 'You have exceeded the allowed order time today. The number of times you can order is [number_times]', 'wlo' ) : $wlo_user_options['message'];
            $customer_settings['apply_for_all'] = isset( $wlo_user_options['apply_for_all'] ) ? $wlo_user_options['apply_for_all'] : '';
            $customer_settings['apply_for_users'] = isset( $wlo_user_options['apply_for_users'] ) ? $wlo_user_options['apply_for_users'] : '';
            $customer_settings['limit_users'] = isset( $wlo_user_options['limit_users'] ) ? $wlo_user_options['limit_users'] : array();
            $customer_settings['apply_for_roles'] = isset( $wlo_user_options['apply_for_roles'] ) ? $wlo_user_options['apply_for_roles'] : 'no';
            $customer_settings['list_role'] = isset( $wlo_user_options['list_role'] ) ? $wlo_user_options['list_role'] : array();
            $customer_settings['role_time_number'] = ! isset( $wlo_user_options['role_time_number'] ) ? array() : $wlo_user_options['role_time_number'] ;
            $customer_settings['apply_for_guest'] = isset( $wlo_user_options['apply_for_guest'] ) ? $wlo_user_options['apply_for_guest'] : '';
            return $customer_settings;
        }
    }
}