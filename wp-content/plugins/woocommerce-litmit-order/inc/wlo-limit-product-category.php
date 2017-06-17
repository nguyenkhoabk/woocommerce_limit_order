<?php
/**
 *  Check condition logic about limit product category
 */


if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_LIMIT_PRODUCT_CATEGORY' ) )
{
    /**
     * Class WLO_LIMIT_PRODUCT_CATEGORY
     */
    class WLO_LIMIT_PRODUCT_CATEGORY
    {
        public static function limit_product_category()
        {
            $wlo_product_category_settings = WLO_Product_Category_Settings::get_settings();
            $active_limit_product_category = isset( $wlo_product_category_settings['limit_on_product_category'] ) ? $wlo_product_category_settings['limit_on_product_category'] : false;
            if ( $active_limit_product_category )
            {
                $total_rules = isset( $wlo_product_category_settings['total-rows'] ) ? $wlo_product_category_settings['total-rows'] : 0;

                if ( $total_rules )
                {
                    // get total product item in cart
                    $cart_items = WC()->cart->get_cart();
                    $list_errors = array();

                    for ( $i = 0; $i < $total_rules; $i++ )
                    {
                        $row = "row_$i";
                        $limit_rule = $wlo_product_category_settings[$row];
                        $apply_rule = isset( $limit_rule['apply_rule'] ) ? $limit_rule['apply_rule'] : false;
                        $limit_number = $limit_rule['limit_number'];
                        if ( $apply_rule )
                        {
                            // check limit roles
                            if ( ! self::check_user_role( $limit_rule ) )
                            {
                                continue;
                            }
                            // check product category rule
                            $check_date_ranges_result = self::check_date_range( $limit_rule );

                            if ( ! $check_date_ranges_result['belong_range_date'] )
                            {
                                continue;
                            }

                            // get product category and count product purchased in this cat
                            $count_item_terms = array();

                            $args['start_date'] = $check_date_ranges_result['start_date'];
                            $args['to_date'] = $check_date_ranges_result['to_date'];

                            // get order's items in date ranges by user id
                            $items = WLO_Order_Settings::get_order_items_id_by_customer_id( get_current_user_id(), $args );
                            // count amount of product sold belong a product category
                            foreach ( $items as $item )
                            {
                                $item_id = $item->ID;
                                $item_qty = $item->qty;
                                // get product category and count product purchased in this cat
                                $item_terms = get_the_terms( $item_id, 'product_cat' );
                                foreach ($item_terms as $term)
                                {
                                    if ( isset( $count_item_terms[$term->term_id] ) )
                                    {
                                        $count_item_terms[$term->term_id] += intval( $item_qty );
                                    }
                                    else
                                    {
                                        $count_item_terms[$term->term_id] = intval( $item_qty );
                                    }
                                }
                            }

                            // check product's categories of the products in cart
                            foreach ( $cart_items as $product )
                            {
                                $product_id = $product['product_id'];
                                $product_qty = $product['quantity'];
                                $product_name = $product['data']->post->post_title;
                                // get product category
                                $terms = get_the_terms( $product_id, 'product_cat' );
                                $product_cat_id = array();
                                foreach ($terms as $term)
                                {
                                    $product_cat_id[] = $term->term_id;
                                }
                                $product_category_limited = self::get_product_category_limited( $limit_rule, $product_cat_id );
                                if ( ! empty( $product_category_limited ) )
                                {
                                    // check amount of product can purchase
                                    // 1. lay thong tin product category cua san pham
                                    // 2. kiem tra tong so luong san pham KH da mua cua category trong date range
                                    // 3. neu > tong so luong product co thua mua trong category thi hien canh bao

                                    foreach ( $product_category_limited as $cat )
                                    {
                                        // update new amount product purchased in list product category get from order to check with product in cart
                                        if ( isset( $count_item_terms[$cat] ) )
                                        {
                                            $count_item_terms[$cat] += intval( $product_qty );
                                        }
                                        else
                                        {
                                            $count_item_terms[$cat] = intval( $product_qty );
                                        }

                                        if ( $count_item_terms[$cat] > $limit_number )
                                        {
                                            $current_term = get_term_by( 'id', $cat, 'product_cat' );
                                            $limit_rule_message = $wlo_product_category_settings['limit_product_category_message'];
                                            $limit_rule_message = str_replace( '[total_number]', $count_item_terms[$cat], $limit_rule_message );
                                            $limit_rule_message = str_replace( '[product_category]', $current_term->name, $limit_rule_message );
                                            $limit_rule_message = str_replace( '[from_date_range]', date( 'm/d/Y', strtotime( $args['start_date'] ) ), $limit_rule_message );
                                            $limit_rule_message = str_replace( '[to_date_range]', date( 'm/d/Y', strtotime( $args['to_date'] ) ), $limit_rule_message );
                                            $limit_rule_message = str_replace( '[limit_number]', empty( $limit_number ) ? 0 : $limit_number, $limit_rule_message );
                                            $list_errors[] = $limit_rule_message;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // display total messages
                    foreach ( $list_errors as $error )
                    {
                        wc_add_notice( $error , 'error' );
                    }
                }
            }
        }

        /**
         * Check current user role belong in limit rule
         * @param $limit_rule array limit rule
         * @return bool result of check condition
         */
        static function check_user_role( $limit_rule )
        {
            // not apply rule for user don't login
            if ( ! is_user_logged_in() )
            {
                return false;
            }
            // return true if current apply for all roles
            if ( $limit_rule['limit_all_roles'] )
            {
                return true;
            }
            // if not apply rule to any role
            if ( empty( $limit_rule['limit_roles'] ) )
            {
                return false;
            }
            // get current user role
            $current_user_id = get_current_user_id();
            $current_user_data = get_userdata( $current_user_id );
            $current_user_roles = $current_user_data->roles;
            // check current user roles exist in list limit roles
            $is_role_limited = array_intersect( $current_user_roles, $limit_rule['limit_roles'] );
            if ( empty( $is_role_limited ) )
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        /**
         *  Check date ranges
         * @param $limit_rule array limit rule
         * @return bool result of check condition
         */
        static function check_date_range( $limit_rule )
        {
            // if blank the rule will apply for current date
            if ( empty( $limit_rule['date_range'] ) )
            {
                $result['belong_range_date'] = 'yes';
                $result['start_date'] = date('Y/m/d');
                $result['to_date'] = date('Y/m/d');
                return $result;
            }
            // check current date belong date range
            $date_range = $limit_rule['date_range'];
            $result = WLO_FUNCTIONS::check_current_date_belong_date_ranges( $date_range );
            if ( ! isset( $result ) )
            {
                $result['belong_range_date'] = false;
            }
            return $result;
        }

        /**
         *  Check current product categories belong list limit product categories
         * @param $limit_rule array limit rule
         * @param $product_category array product categories
         * @return bool result of check condition
         */
        static function get_product_category_limited( $limit_rule, $product_category )
        {
            $product_categories_limited = array_intersect( $product_category, $limit_rule['categories'] );
            return $product_categories_limited;
        }
    }
}