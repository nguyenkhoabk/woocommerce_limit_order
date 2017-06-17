<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_FUNCTIONS' ) )
{
    /**
     * This class contains common functions
     */
    class WLO_FUNCTIONS
    {
        /**
         * Get IP address of client
         * @return string
         */
        public static function get_ip()
        {
            if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) )
            {
                //check ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];

            }
            elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
            {
                //to check ip is pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else
            {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }

        /**
         * Sub specific amount date
         * @param $date         string date need to execute
         * @param $number_day   int the number date need to sub
         * @param $type         string type interval day, month, year
         * @return string       date result
         */
        public static function sub_specific_date( $date, $number_day, $type )
        {
            $date_convert = new DateTime( $date );
            $date_convert->sub( new DateInterval('P' . $number_day . $type) );
            $result = $date_convert->format('Y-m-d');
            return $result;
        }

        /**
         * Add specific amount date
         * @param $date         string  date    need to execute
         * @param $number_day   int     the     number date need to add
         * @param $type         string  type    interval day, month, year
         * @return string       date    result  new date
         */
        public static function add_specific_date( $date, $number_day, $type )
        {
            $date_convert = new DateTime( $date );
            $date_convert->add( new DateInterval('P' . $number_day . $type) );
            $result = $date_convert->format('Y-m-d');
            return $result;
        }

        /**
         * Check today belong limit dates range
         * @param $date_range string poduct date ranges
         * @return $result $args array date range information
         */
        public static function check_current_date_belong_date_ranges( $date_ranges )
        {
            $current_date = date("m/d/Y");
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
                        $args['belong_range_date'] = 'yes';
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