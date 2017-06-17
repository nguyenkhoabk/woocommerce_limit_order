<?php
/**
 * General Setting
 * Functions get setting datas in Settings tab
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_General_Settings' ) )
{
    /**
     * Class WLO_General_Settings
     */
    class WLO_General_Settings
    {

        /**
         * Return general settings
         * @return $general_settings array general settings
         */

        public static function get_settings()
        {
            $wlo_options = get_option( WLO_OPION );
            $general_settings['advance_date'] = isset( $wlo_options['advance']['date'] ) ? $wlo_options['advance']['date'] : '';
            $general_settings['advance_selected_date'] = isset( $wlo_options['advance']['selected_date'] ) ? $wlo_options['advance']['selected_date'] : '';
            $general_settings['advance_time'] = isset( $wlo_options['advance']['time'] ) ? $wlo_options['advance']['time'] : '';
            $general_settings['advance_time_selected'] = isset( $wlo_options['advance']['time_selected'] ) ? $wlo_options['advance']['time_selected'] : '';
            $general_settings['link_text'] = isset( $wlo_options['link_text'] ) ? $wlo_options['link_text'] : '';
            $general_settings['link_url'] = isset( $wlo_options['link_url'] ) ? $wlo_options['link_url'] : '#';
            $general_settings['advance_enable_range_time'] = isset( $wlo_options['advance']['enable_range_date'] ) ? $wlo_options['advance']['enable_range_date'] : '';
            $general_settings['advance_date_ranges'] = isset( $wlo_options['advance']['date_ranges'] ) ? $wlo_options['advance']['date_ranges'] : '';
            return $general_settings;
        }

        /**
         * Check today need to limit order
         * @return $result bool true if today need to limit order
         */
        public static function is_limit_day()
        {
            // get general options
            $general_settings = self::get_settings();

            if( 'yes' == $general_settings['advance_date'] )
            {
                // If current date exists in list dates selected
                if(  ! empty( $general_settings['advance_selected_date'] ) && false === strpos( $general_settings['advance_selected_date'], date("m/d/Y") ) )
                {
                    return false;
                }
            }

            return true;
        }

        /**
         * Check today belong limit dates range
         * @return $result $args array date range information
         */
        public static function is_belong_date_range()
        {
            // get general options
            $general_settings = self::get_settings();
            // date range information
            $args = array(
                'limit_range_date'  =>  'no',
            );
            if( 'yes' == $general_settings['advance_enable_range_time'] )
            {
                $current_date = date("m/d/Y");
                $date_ranges = $general_settings['advance_date_ranges'];
                $date_range_arr = explode( "\n", $date_ranges );
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
            }

            return $args;
        }
        /**
         * Checks option limit range date
         * @return $args array setting values
         */
        public static function enable_limit_range_date()
        {
            $args = array(
                'enable'    =>  'no',
            );
            // get general options
            $general_settings = self::get_settings();
            if( 'yes' == $general_settings['advance_enable_range_time'] )
            {
                $args['enable'] = 'yes';
                $args['date_ranges'] = $general_settings['advance_date_ranges'];
            }
            return $args;
        }

        /**
         * Check current time is in range time need to limit order
         * @return $result bool true if current time is in range time need to limit order
         */
        public static function enable_limit_range_time()
        {
            // get general options
            $general_settings = self::get_settings();

            if( 'yes' == $general_settings['advance_time'] )
            {
                return true;
            }
            return false;
        }

        /**
         * Check and return url combined with target link
         * @param $message  string  message display
         * @return $message string  new message combined with target page
         */
        public static function add_target_link( $message )
        {
            // get general options
            $general_settings = self::get_settings();

            // add target page to message
            $target_link_text = trim( $general_settings['link_text'] );
            if( ! empty( $target_link_text ) )
            {
                $target_link_url = trim( $general_settings['link_url'] );
                if( empty( $target_link_url ) )
                {
                    $target_link_url = '#';
                }
                $message .= sprintf( ' <a href="' . $target_link_url . '"> ' . $target_link_text . ' </a>' );
            }
            return $message;
        }
    }
}