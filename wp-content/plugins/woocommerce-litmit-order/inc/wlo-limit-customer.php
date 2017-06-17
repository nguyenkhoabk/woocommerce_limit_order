<?php
/**
 *  Check condition logic about limit customer order
 */


if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_LIMIT_CUSTOMER' ) )
{
    /**
     * Class WLO_LIMIT_CUSTOMER
     */
    class WLO_LIMIT_CUSTOMER
    {
        /**
         * Save information after an order just have made
         *
         * */
        public static function check_order_log( $order_id )
        {
            $wlo_user_options = WLO_Customer_Settings::get_settings();
            //check if enable limit customer
            $is_enable_limit_cutomer = $wlo_user_options['limit_on_identity'];
            if ( 'yes' == $is_enable_limit_cutomer )
            {
                // Time create
                $current_date = date( 'Y-m-d H:i:s' );
                // If user logged in so get user ID, if not get client ip address
                if( is_user_logged_in() )
                {
                    // Get User ID
                    $id = get_current_user_id();
                }
                else  // if user is guest
                {
                    // Get IP
                    $id = WLO_FUNCTIONS::get_ip();
                }
                // get billing information
                $billing_name = sprintf( '%s %s', get_post_meta( $order_id, '_billing_first_name', true ), get_post_meta( $order_id, '_billing_last_name', true ) );
                $billing_phone = get_post_meta( $order_id, '_billing_phone', true );
                $billing_email = get_post_meta( $order_id, '_billing_email', true );
                $args = array(
                    'id'            =>  $id,
                    'order_date'    =>  $current_date,
                    'count_order'   =>  '1',
                    'cus_name'      =>  $billing_name,
                    'cus_phone'     =>  $billing_phone,
                    'cus_email'     =>  $billing_email
                );
                // Save order information just created
                WLO_DBHELPER::insert_order_log( $args );
            }
        }

        /**
         * Check how many times order was made by user
         *
         * */
        public static function check_valid_order_time()
        {
            $general_settings = WLO_General_Settings::get_settings();
            $wlo_user_options = WLO_Customer_Settings::get_settings();
            //check if enable limit customer
            $is_enable_limit_cutomer = $wlo_user_options['limit_on_identity'];
            if( 'yes' == $is_enable_limit_cutomer )
            {
                $limit_order_time = $wlo_user_options['limit_time_for_customer'];
                $message = $wlo_user_options['message'];
                $args = array();
                $current_date = date( 'Y-m-d' );
                // option limit order on specific dates is enabled
                $args['order_date'] = $current_date;
                $args['id'] = '';

                // If current date exists in list dates selected
                if( false === WLO_General_Settings::is_limit_day() )
                {
                    $args['order_date'] = NULL;
                }
                // If enabled limit range date
                $result = WLO_General_Settings::enable_limit_range_date();
                if( 'yes' == $result['enable'] )
                {
                    $date_ranges = $result['date_ranges'];
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
                                $args['order_date'] = NULL;
                                $args['limit_range_date'] = 'yes';
                                $args['start_date'] = date( 'Y-m-d', strtotime( $start_date ) );
                                $args['to_date'] = date( 'Y-m-d', strtotime( $to_date ) );
                                break; // exit when match first date range
                            }
                        }
                    }
                }
                // option limit order on range time is enabled
                if( WLO_General_Settings::enable_limit_range_time() )
                {
                    $advance_time_selected_arr = explode( '-', $general_settings['advance_time_selected'] );
                    $from_time = $advance_time_selected_arr[0];
                    $to_time = $advance_time_selected_arr[1];
                    $args['limit_on_time'] = 'yes';
                    $args['from_time'] = $from_time;
                    $args['to_time'] = $to_time;
                }
                else
                {
                    $args['limit_on_time'] = 'no';
                }

                // Limit Order on all transactions is enabled
                if( 'yes' == $wlo_user_options['apply_for_all'] )
                {
                    // get all order logs
                    $args['id'] = -1;
                }
                else
                {
                    if( is_user_logged_in() )
                    {
                        // if limit order by roles enabled
                        $current_user_id = get_current_user_id();
                        // get current user role
                        $current_user_data = get_userdata( $current_user_id );
                        $current_user_roles = $current_user_data->roles;
                        // check list members limit
                        if( 'yes' == $wlo_user_options['apply_for_users'] )
                        {
                            // If current user exists in list users are limited
                            if( in_array( $current_user_id, $wlo_user_options['limit_users'] ) )
                            {
                                $args['id'] = $current_user_id;
                            }
                        }
                        // if customer does not belong to list limit member, continue to check with list roles are limited
                        if ( empty( $args['id'] ) )
                        {
                            // limit all roles
                            if( 'all' == $wlo_user_options['apply_for_roles'] )
                            {
                                $args['id'] = $current_user_id;
                            }
                            elseif ( 'specific' == $wlo_user_options['apply_for_roles'] ) // limit specific role
                            {
                                // list roles limited
                                $is_role_limited = array_intersect( $current_user_roles, $wlo_user_options['list_role'] );
                                if( ! empty( $is_role_limited ) )
                                {
                                    $args['id'] = $current_user_id;

                                    // get number of times can order of this role
                                    // get maximum number of times value
                                    if( ! empty( $wlo_user_options['role_time_number'][$is_role_limited[0]] ) )
                                    {
                                        $limit_order_time = $wlo_user_options['role_time_number'][$is_role_limited[0]];
                                    }
                                    // get max number of times can order in list roles
                                    foreach (  $is_role_limited as $role_limited )
                                    {
                                        $number_time_can_order = $wlo_user_options['role_time_number'][$role_limited];
                                        if (  ! empty( $number_time_can_order ) && $limit_order_time < $number_time_can_order )
                                        {
                                            $limit_order_time = $number_time_can_order;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        if( 'yes' == $wlo_user_options['apply_for_guest'] )
                        {
                            // check number time order by ip
                            $user_ip = WLO_FUNCTIONS::get_ip();
                            $args['id'] = $user_ip;
                        }
                    }
                }

                // get order log
                $row = WLO_DBHELPER::get_order_log( $args );

                if ( $row )
                {
                    // count time order
                    $count_order = count( $row );
                    // count time order more than or equal limit order time
                    if ( $count_order >= $limit_order_time )
                    {
                        $target_link_text = trim( $general_settings['link_text'] );
                        if( ! empty( $target_link_text ) )
                        {
                            $target_link_url = trim( $general_settings['link_url'] );
                            if( empty( $target_link_url ) )
                            {
                                $target_link_url = '#';
                            }
                            $message .= sprintf( '<a href="' . $target_link_url . '"> ' . $target_link_text . ' </a>' );
                        }
                        $message = str_replace( '[number_times]',$limit_order_time , $message);
                        wc_add_notice( $message , 'error' );
                    }
                }
            }
        }

        /**
         * Get list order log in a range time
         * @param $range_time string
         * @param $page_num   int use for pagination
         * @param $post_each_page   int number order id displayed
         * @param $post_each_page   string number order return
         * @return $result    array  List Order log
         */
        public static function get_order_log( $range_time, $page_num, $post_each_page, $number )
        {
            global $wpdb;
            $table_name = WLO_DBHELPER::get_table_name();
            $sql = 'SELECT id , cus_name, cus_email, order_date, count( id ) "count" FROM ' . $table_name;
            $current_date = date( 'Y-m-d' );
            if(  'today' == $range_time )
            {
                $sql = $wpdb->prepare( $sql . ' WHERE DATE( order_date )=%s', $current_date );
            }
            elseif( 'one_week' == $range_time )
            {
                $from_date = WLO_FUNCTIONS::sub_specific_date( $current_date, 7, 'D' );
                $sql = $wpdb->prepare( $sql . ' WHERE DATE( order_date ) between %s and %s', $from_date, $current_date );
            }
            elseif( 'one_month' == $range_time )
            {
                $from_date = WLO_FUNCTIONS::sub_specific_date( $current_date, 1, 'M' );
                $sql = $wpdb->prepare( $sql . ' WHERE DATE( order_date ) between %s and %s', $from_date, $current_date );
            }
            elseif( 'one_year' == $range_time )
            {
                $from_date = WLO_FUNCTIONS::sub_specific_date( $current_date, 1, 'Y' );
                $sql = $wpdb->prepare( $sql . ' WHERE DATE( order_date ) between %s and %s', $from_date, $current_date );
            }
            // add pagination
            if( 'all' == $number )
            {
                $sql .= ' GROUP BY DATE( order_date ), id';
            }
            else
            {
                $sql = $wpdb->prepare( $sql . ' GROUP BY DATE( order_date ), id LIMIT %d, %d', $page_num * $post_each_page, $post_each_page );
            }
            $result = WLO_DBHELPER::execute_query( $sql );
            return $result;
        }

    }
}