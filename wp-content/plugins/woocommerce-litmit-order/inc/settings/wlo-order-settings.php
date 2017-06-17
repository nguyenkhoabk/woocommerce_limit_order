<?php
/**
 * Order Settings
 * Functions get order data
 */

if( ! class_exists( 'WLO_Order_Settings' ) )
{
    /**
     * Class WLO_Order_Settings
     */
    class WLO_Order_Settings
    {
        /**
         * Get all order of customer
         * @param $cus_id int customer id
         * @param $args array filters query
         * @return $orders array customer's orders
         */
        public static function get_orders_by_customer_id ( $cus_id, $args )
        {
            global $wpdb;
            $sql = $wpdb->prepare( "SELECT *
			FROM $wpdb->posts as posts

			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id

			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ( %s )
			AND     posts.post_status   IN ( 'wc-processing', 'wc-completed' )
			AND     meta.meta_value          = %s
			AND     DATE( posts.post_modified ) BETWEEN %s AND %s
		    ", implode( "','", wc_get_order_types( 'order-count' ) ), $cus_id, $args['start_date'], $args['to_date'] );
            $orders = WLO_DBHELPER::execute_query( $sql );
            return $orders;
        }

        /**
         * Get all order's products of a customer
         * @param $cus_id int customer id
         * @param $args array filters query
         * @return $orders array customer's orders
         */
        public static function get_order_items_id_by_customer_id( $cus_id, $args )
        {
            global $wpdb;
            $sql = $wpdb->prepare( "
            SELECT dtl.order_id,
            MAX(dtl.ID) as ID,
            MAX(dtl.qty) as qty
            FROM 
                ( SELECT 
                  o.order_id,
                  o.order_item_id, 		
                  CASE i.meta_key WHEN '_product_id' THEN i.meta_value END  as ID, 
	              CASE i.meta_key WHEN '_qty' THEN i.meta_value END  as qty
                FROM {$wpdb->prefix}woocommerce_order_items o INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta i ON o.order_item_id = i.order_item_id
                WHERE ( i.meta_key = '_product_id' OR i.meta_key = '_qty' ) AND o.order_id IN (
                    SELECT posts.id
                    FROM $wpdb->posts as posts
        
                    LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
        
                    WHERE   meta.meta_key       = '_customer_user'
                    AND     posts.post_type     IN ( 'shop_order' )
                    AND     posts.post_status   IN ( 'wc-processing', 'wc-completed' )
                    AND     meta.meta_value          = %s
                    AND     DATE( posts.post_modified ) BETWEEN %s AND %s ) ) dtl
            GROUP BY dtl.order_id, dtl.order_item_id
            ", $cus_id, $args['start_date'], $args['to_date'] );
            $product_ids = WLO_DBHELPER::execute_query( $sql );
            return $product_ids;
        }
    }

}