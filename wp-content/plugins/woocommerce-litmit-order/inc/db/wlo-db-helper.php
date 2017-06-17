<?php
/**
 *
 * Class init data table when activate plugin
 *
 */
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( !class_exists( 'WLO_DBHELPER' ) )
{
    /**
     * Class contains functions to manipulate data in database
     *
     * */
    class WLO_DBHELPER
    {
        /**
         *  Get table name
         *  @return string
         * */
        public static function get_table_name()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . TABLE_NAME;
            return $table_name;
        }

        /**
         *  Insert an new order log
         *  @param array $args
         *  @return void
         * */
        public static function insert_order_log( $args )
        {
			global $wpdb;
			$table_name = self::get_table_name();
	        $format =   array(
		        '%s',
		        '%s',
		        '%d',
                '%s',
                '%s',
                '%s'
	        );
	        $wpdb->insert( $table_name, $args, $format );
        }

        /**
         *  Get order log
         *  @param array $args
         *  @return object $row
         * */
	    public static function get_order_log( $args )
	    {
		    global $wpdb;
		    $table_name = self::get_table_name();

            if( ! is_null( $args['order_date'] ) )
            {
                $date_created = date( "Y-m-d", strtotime( $args['order_date'] ) );
                $sql = $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE DATE(order_date) = %s', $date_created );
            }
            else
            {   if( 'yes' == $args['limit_range_date'] )
                {
                    $sql = $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE DATE(order_date) BETWEEN %s AND %s', $args['start_date'], $args['to_date'] );
                }
                else
                {
                    $sql = $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE DATE(order_date) = NULL', '' );
                }
            }

            if( $args['limit_on_time'] == 'yes' )
            {
                $current_hour = date( 'H' );
                if( intval( $current_hour ) >= intval( $args['from_time'] ) && intval( $current_hour ) <= intval( $args['to_time'] ) )
                {
                    $sql = $wpdb->prepare( $sql . ' AND HOUR(order_date) BETWEEN %d AND %d', intval( $args['from_time'] ), intval( $args['to_time'] ) );
                }
                else
                {
	                $sql = $wpdb->prepare( $sql . ' AND HOUR(order_date) = -1', '', '' );
                }
            }

            if( $args['id'] != -1 )
            {
                $sql = $wpdb->prepare( $sql . ' AND id = %d', $args['id'] );
            }
            $row = $wpdb->get_results( $sql );
		    return $row;
	    }

        /**
         *  Update order log
         *  @param array $args
         *  @return void
         * */
	    public static function update_order_log( $args )
	    {
		    global $wpdb;
		    $table_name = self::get_table_name();
		    $row = self::get_order_log( $args );
		    // check order exist
		    if( $row != null )
		    {
                $date_created = date( "Y-m-d", strtotime( $args['order_date'] ) );
			    $where_condition = array(
				    'id'            =>  $args['id'],
				    'order_date'  =>  $date_created
			    );
			    // count more order log
			    $count_order = $row->count_order;
				$count_order++;
			    $new_data = array(
				    'count_order'   =>  $count_order,
			    );
			    // update data
			    $wpdb->update( $table_name, $new_data, $where_condition );
		    }
		    else
		    {
			    // add new order log
				self::insert_order_log( $args );
		    }
	    }

        /**
         *  Execute a query statement
         *  @param array $args
         *  @return void
         * */
        public static function execute_query( $sql )
        {
            global $wpdb;
            $row = $wpdb->get_results( $sql );
            return $row;
        }

        /**
         *  Drop table in database
         *  @return void
         * */
        public static function drop_table()
        {
            global $wpdb;
            $table_name = self::get_table_name();
            $wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %s", $table_name ) );
        }
    }
}