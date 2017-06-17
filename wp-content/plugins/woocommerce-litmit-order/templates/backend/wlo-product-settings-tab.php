<?php

if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if( ! class_exists( 'WLO_PRODUCT_SETTINGS_TAB' ) )
{
    /**
     * Class WLO_PRODUCT_SETTINGS_TAB
     * Generate product settings tab
     */
    class WLO_PRODUCT_SETTINGS_TAB
    {
        function __construct()
        {
            // Register User settings
            add_action( 'admin_init', array( $this, 'register_limit_product_settings' ) );
            // add limit quantity product settings to product data
            add_action( 'woocommerce_product_options_advanced', array( $this, 'add_product_meta' ) );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_meta' ) );
        }

        /**
         * Add sections, fields on settings page and register settings
         * */
        function register_limit_product_settings()
        {
            add_settings_section('wlo_settings_limit_product_section', __('', 'wlo'), array( $this, 'setting_section_callback' ) , 'wlo_limit_product_settings_page' );
            add_settings_field('wlo_enable_limit_product_field', __('Limit Order Type', 'wlo'), array( $this, 'setting_enable_limit_order_product_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_amount_product_field', __('Limit amount product can order', 'wlo'), array( $this, 'setting_enable_limit_amount_product_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_max_amount_product_message', __('Notification message when maximum amount product reached', 'wlo'), array( $this, 'setting_maximum_amount_reached_message_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_min_amount_product_message', __('Notification message when minimum amount product not reached', 'wlo'), array( $this, 'setting_minimum_amount_reached_message_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_qty_product_fields', __('Limit quantity of a product on per order', 'wlo'), array( $this, 'setting_limit_qty_product_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_max_qty_product_message', __('Notification message when maximum quantity of a product reached', 'wlo'), array( $this, 'setting_maximum_qty_reached_message_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_min_qty_product_message', __('Notification message when minimum quantity of a product not reached', 'wlo'), array( $this, 'setting_minimum_qty_reached_message_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_total_amount_product_on_cross_fields', __('Limit total quantity product can purchase', 'wlo'), array( $this, 'setting_limit_total_amount_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
            add_settings_field('wlo_limit_total_amount_product_message', __('Notification message when exceed total amount of a product can purchase across all orders', 'wlo'), array( $this, 'setting_total_amount_exceed_message_callback' ) , 'wlo_limit_product_settings_page', 'wlo_settings_limit_product_section' );
        }

        function setting_section_callback()
        {
            ?>
            <?php
        }

        /**
         * Display settings for limit total amount product
         */
        function setting_limit_total_amount_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );

            if( ! isset( $wlo_product_options['limit_total_qty_product_types'] ) )
            {
                $selected = 'all_products';
            }
            else
            {
                $selected = $wlo_product_options['limit_total_qty_product_types'];
            }

            $enable_limit_product_purchase = '';
            if ( isset( $wlo_product_options['enable_limit_product_purchase'] ) )
            {
                $enable_limit_product_purchase = $wlo_product_options['enable_limit_product_purchase'];
            }
        ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_product_options[enable_limit_product_purchase]" value="yes" <?php checked( $enable_limit_product_purchase, 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
                <p class="wlo-field-description">
                    <?php _e( 'Determines total quantity of a product can purchase across all orders', 'wlo' ); ?>
                </p>
            </div>

            <div class="wlo-rows">
                <p>
                    <label><input type="radio" name="_wlo_product_options[limit_total_qty_product_types]" value="specific_product" <?php checked( 'specific_product', $selected ); ?> > <?php _e( 'Apply on a specific product', 'wlo' ) ?></label>
                </p>
                <p>
                    <label><input type="radio" name="_wlo_product_options[limit_total_qty_product_types]" value="all_products" <?php checked( 'all_products', $selected ); ?> > <?php _e( 'Apply on all products', 'wlo' ) ?></label>
                </p>
                <p class="wlo-field-description"><?php _e( 'Using below settings for all products', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows wlo-sub-option">
                <input name="_wlo_product_options[global_total_qty_product]" type="number" min="0" value="<?php echo NULL === $wlo_product_options['global_total_qty_product'] ? 10 : $wlo_product_options['global_total_qty_product']; ?>" class="regular-text"><?php _e( 'The total quantity product can purchase across all orders', 'wlo' ); ?>
                <p class="wlo-field-description"><?php _e( 'Ex: if value is 10 so total quantity can purchase of a product across all orders must be less than or equal 10, greater is invalid. Set value is blank or 0 to unlimit ', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows">
            </div>
        <?php
        }

        /**
         * Display options in setting limit quantity product
         */
        function setting_limit_qty_product_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            $selected = $wlo_product_options['limit_qty_product_types'];
            if( ! isset( $selected ) )
            {
                $selected = 'all_products';
            }
            ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_product_options[enable_limit_qty_product]" value="yes" <?php checked( isset( $wlo_product_options['enable_limit_qty_product'] ) ? $wlo_product_options['enable_limit_qty_product'] : '', 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
                <p class="wlo-field-description">
                    <?php _e( 'This option determine quantity of a product can place on per order', 'wlo' ); ?>
                </p>
            </div>
            <div class="wlo-rows">
                <p>
                    <label><input type="radio" name="_wlo_product_options[limit_qty_product_types]" value="specific_product" <?php checked( 'specific_product', $selected ); ?> > <?php _e( 'Apply on a specific product', 'wlo' ) ?></label>
                </p>
                <p>
                    <label><input type="radio" name="_wlo_product_options[limit_qty_product_types]" value="all_products" <?php checked( 'all_products', $selected ); ?> > <?php _e( 'Apply on all products', 'wlo' ) ?></label>
                </p>
                <p class="wlo-field-description"><?php _e( 'Using below settings for all products', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows wlo-sub-option">
                <input name="_wlo_product_options[global_product_quantity_max]" type="number" min="0" value="<?php echo $wlo_product_options['global_product_quantity_max'] == NULL ? 10 : $wlo_product_options['global_product_quantity_max']; ?>" class="regular-text"><?php _e( 'Maximum quantity', 'wlo' ); ?>
                <p class="wlo-field-description"><?php _e( 'Ex: if value is 10 so quantity of a product on order must be less than or equal 10, greater is invalid. Set value is blank or 0 to unlimit ', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows wlo-sub-option">
                <input name="_wlo_product_options[global_product_quantity_min]" type="number" min="0" value="<?php echo $wlo_product_options['global_product_quantity_min'] == NULL ? 2 : $wlo_product_options['global_product_quantity_min']; ?>" class="regular-text"><?php _e( 'Minimum quantity', 'wlo' ); ?>
                <p class="wlo-field-description"><?php _e( 'Ex: if value is 2 so quantity of a product on order must be more than or equal 2, less than 2 is invalid. Set value is blank or 0 to unlimit ', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows">
            </div>
            <?php
        }
        /**
         * Dislay checkbox enable limit order product
         */
        function setting_enable_limit_order_product_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            $limit_on_product = '';
            if ( isset( $wlo_product_options['limit_on_product'] ) )
            {
                $limit_on_product = $wlo_product_options['limit_on_product'];
            }
            ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_product_options[limit_on_product]" value="yes" <?php checked( $limit_on_product, 'yes' ); ?> >
                    <?php _e( 'Enable limit order product', 'wlo' ); ?>
                </label>
            </div>
            <?php
        }

        /**
         * Display option fields use for limit amount product
         */
        function setting_enable_limit_amount_product_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_product_options[enable_limit_amount_product]" value="yes" <?php checked( isset( $wlo_product_options['enable_limit_amount_product'] ) ? $wlo_product_options['enable_limit_amount_product'] : '', 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
                <p class="wlo-field-description">
                    <?php _e( 'Determines amount products can place on a order', 'wlo' ); ?>
                </p>
            </div>
            <div class="wlo-rows wlo-sub-option">
                <input name="_wlo_product_options[max_amount]" type="number" min="0" value="<?php echo $wlo_product_options['max_amount'] === NULL ? 5 : $wlo_product_options['max_amount']; ?>" class="regular-text"><?php _e( 'Maximum amount product can order', 'wlo' ); ?>
                <p class="wlo-field-description"><?php _e( 'Ex: if value is 5 so customer can buy five different products on a order, more than five different one is invalid. Set value is blank or 0 to unlimit ', 'wlo' ); ?></p>
            </div>
            <div class="wlo-rows wlo-sub-option">
                <input name="_wlo_product_options[min_amount]" type="number" min="0" value="<?php echo $wlo_product_options['min_amount'] === NULL ? 2 : $wlo_product_options['min_amount']; ?>" class="regular-text"><?php _e( 'Minimum amount product can order', 'wlo' ); ?>
                <p class="wlo-field-description"><?php _e( 'Ex: if value is 2 so customer must buy at least 2 different products on a order, less than two different one is invalid. Set value is blank or 0 to unlimit ', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         *  Message field uses for manimum amount product reached
         */
        function setting_maximum_amount_reached_message_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_options[max_amount_message]" class="wlo-message" class="regular-text"><?php echo $wlo_product_options['max_amount_message'] === NULL ? __( 'There are [total_cart_items] in cart, exceeded maximum amount product can order is [max_amount]', 'wlo' ) : $wlo_product_options['max_amount_message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'Use <strong>[max_amount]</strong> to show maximum total amount product can order, <strong>[total_cart_items]</strong> to show total items in cart', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         *  Message field uses for manimum amount product reached
         */
        function setting_minimum_amount_reached_message_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_options[min_amount_message]" class="wlo-message" class="regular-text"><?php echo $wlo_product_options['min_amount_message'] === NULL ? __( 'There are [total_cart_items] in cart, not reach minimum amount product must order is [min_amount]', 'wlo' ) : $wlo_product_options['min_amount_message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'Use <strong>[min_amount]</strong> to show minimum total amount product can order, <strong>[total_cart_items]</strong> to show total items in cart', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         *  Message field uses for manimum amount product reached
         */
        function setting_maximum_qty_reached_message_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_options[max_qty_message]" class="wlo-message" class="regular-text"><?php echo $wlo_product_options['max_qty_message'] === NULL ? __( 'The product [product_name] placed [product_qty] and exceeded maximum quantity a product can buy is [max_qty]', 'wlo' ) : $wlo_product_options['max_qty_message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'Use <strong>[max_qty]</strong> to show maximum quantity of a product can buy, <strong>[product_name]</strong> to show product name, <strong>[product_qty]</strong> to show product quantity in order', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         *  Message field uses for manimum amount product reached
         */
        function setting_minimum_qty_reached_message_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_options[min_qty_message]" class="wlo-message" class="regular-text"><?php echo $wlo_product_options['min_qty_message'] === NULL ? __( 'The product [product_name] placed [product_qty] and not reach minimum quantity a product must buy is [min_qty]', 'wlo' ) : $wlo_product_options['min_qty_message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'Use <strong>[min_qty]</strong> to show minimum quantity of a product must buy, <strong>[product_name]</strong> to show product name, <strong>[product_qty]</strong> to show product quantity in order', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         *  Message field uses for total amount of a exceed
         */
        function setting_total_amount_exceed_message_callback()
        {
            $wlo_product_options = get_option( WLO_PRODUCT_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_options[total_qty_message]" class="wlo-message" class="regular-text"><?php echo $wlo_product_options['total_qty_message'] === NULL ? __( 'The [product_name] purchased total quantity [total_qty_purchased] across all orders from [from_date_range] to [to_date_range], you just can buy [total_qty] so you can not buy [current_qty] in this order.', 'wlo' ) : $wlo_product_options['total_qty_message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'Use <strong>[total_qty_purchased]</strong> to show total quantity of a product purchased across all orders, <strong>[product_name]</strong> to show product name, <strong>[total_qty] </strong> to show total quantity of a product can purchase across all orders, <strong>[current_qty]</strong> to show quantity of a product in current order, <strong>[from_date_range]</strong> to show start date in date range , <strong>[to_date_range]</strong> to show end date in date range', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         * Add more metabox to product meta on product's data
         */
        function add_product_meta()
        {
        ?>
            <div class="options_group">
            <?php
            // Limit quantity of a product can order
            woocommerce_wp_checkbox(
                array(
                    'id' => 'wlo_enable_limit_qty',
                    'label' => __( 'Enable limit order quantity product', 'woocommerce' ),
                    'description'   =>  __( 'Limit quantity of this product can purchase on per order', 'wlo' ),
                    'desc_tip'      =>  'true',
                )
            );
            woocommerce_wp_text_input(
                array(
                    'id'            =>  'wlo_max_qty',
                    'label'         =>  __( 'Maximum quantity', 'wlo' ),
                    'description'   =>  __( 'If value is 10 so quantity of a product on order must be less than 10, greater is invalid. Set value is blank or 0 to unlimit ', 'wlo' ),
                    'desc_tip'      =>  'true',
                )
            );
            woocommerce_wp_text_input(
                array(
                    'id'            =>  'wlo_min_qty',
                    'label'   =>  __( 'Minimum quantity' ),
                    'description'   =>  __( 'If value is 2 so quantity of a product on order must be more than 2, less than 2 is invalid. Set value is blank or 0 to unlimit ', 'wlo' ),
                    'desc_tip'      =>  'true',
                )
            );
            // Limit total quantity of a product cross all order
            woocommerce_wp_checkbox(
                array(
                    'id' => 'wlo_enable_limit_total_qty',
                    'label' => __( 'Enable limit total quantity product', 'woocommerce' ),
                    'description'   =>  __( 'Limit total quantity of this product can purchase across all orders', 'wlo' ),
                    'desc_tip'      =>  'true',
                )
            );
            woocommerce_wp_text_input(
                array(
                    'id'            =>  'wlo_total_qty',
                    'label'         =>  __( 'The total quantity product can purchase across all orders', 'wlo' ),
                    'description'   =>  __( 'If value is 10 so total quantity can purchase of a product across all orders must be less than or equal 10, greater is invalid. Set value is blank or 0 to unlimit ', 'wlo' ),
                    'desc_tip'      =>  'true',
                )
            );
            // Limit total quantity of a product cross all order
            woocommerce_wp_checkbox(
                array(
                    'id' => 'wlo_enable_limit_in_date_range',
                    'label' => __( 'Apply limit rules in date ranges', 'woocommerce' ),
                    'description'   =>  __( 'Enable', 'woocommerce' ),
                )
            );
            woocommerce_wp_textarea_input(
                array(
                    'id'            =>  'wlo_date_range_limit',
                    'label'         =>  __( 'Date Ranges', 'wlo' ),
                    'description'   =>  __( '<br />You can enter <strong>only one rule per line</strong> with format <strong>mm/dd/yyyy - mm/dd/yyyy</strong> . Ex: 06/12/2016 - 06/30/2016', 'wlo' ),
                )
            );
            ?>
            </div>

        <?php
        }

        /**
         * Save post meta
         * @param $post_id int post id
         */
        function save_product_meta( $post_id )
        {
            // save option enable limit qty
            $woo_enable_limit_qty_product = isset( $_POST['wlo_enable_limit_qty'] ) ? $_POST['wlo_enable_limit_qty'] : 'no';
            update_post_meta( $post_id, 'wlo_enable_limit_qty', esc_attr( $woo_enable_limit_qty_product ));
            // save option enable limit total qty
            $wlo_enable_limit_total_qty = isset( $_POST['wlo_enable_limit_total_qty'] ) ? $_POST['wlo_enable_limit_total_qty'] : 'no';
            update_post_meta( $post_id, 'wlo_enable_limit_total_qty', esc_attr( $wlo_enable_limit_total_qty ));
            // save option enable limit by date ranges
            $wlo_enable_limit_in_date_range = isset( $_POST['wlo_enable_limit_in_date_range'] ) ? $_POST['wlo_enable_limit_in_date_range'] : 'no';
            update_post_meta( $post_id, 'wlo_enable_limit_in_date_range', esc_attr( $wlo_enable_limit_in_date_range ));
            $wlo_limit_range_time = $_POST['wlo_date_range_limit'];
            // save max quantity product can order
            $woo_max_qty = $_POST['wlo_max_qty'];
            $woo_min_qty = $_POST['wlo_min_qty'];
            $woo_total_qty = $_POST['wlo_total_qty'];
            if( empty( $woo_max_qty ) )
            {
                $woo_max_qty = 0;
            }
            if( empty( $woo_min_qty ) )
            {
                $woo_min_qty = 0;
            }
            if( $woo_min_qty > 0 && $woo_max_qty > 0 && $woo_min_qty >= $woo_max_qty )
            {
                $woo_min_qty = $woo_max_qty - 1;
            }
            if( empty( $woo_total_qty ) )
            {
                $woo_total_qty = 0;
            }
            update_post_meta( $post_id, 'wlo_max_qty', esc_attr( $woo_max_qty ));
            update_post_meta( $post_id, 'wlo_min_qty', esc_attr( $woo_min_qty ));
            update_post_meta( $post_id, 'wlo_total_qty', esc_attr( $woo_total_qty ));
            update_post_meta( $post_id, 'wlo_date_range_limit', esc_attr( $wlo_limit_range_time ));
        }
        /**
         * Sanitize form values before save
         * @param $input array form values
         * @return mixed
         */
        function sanitize_product_settings( $input )
        {
            if( empty( $input['min_amount'] ) )
            {
                $input['min_amount'] = 0;
            }
            if( empty( $input['max_amount'] ) )
            {
                $input['max_amount'] = 0;
            }
            if( $input['max_amount'] > 0 && $input['min_amount'] > 0 && $input['min_amount'] >= $input['max_amount'] )
            {
                $input['min_amount'] = $input['max_amount'] - 1;
            }

            if( empty( $input['global_product_quantity_min'] ) )
            {
                $input['global_product_quantity_min'] = 0;
            }
            if( empty( $input['global_product_quantity_max'] ) )
            {
                $input['global_product_quantity_max'] = 0;
            }
            if( $input['global_product_quantity_max'] > 0 && $input['global_product_quantity_min'] > 0 && $input['global_product_quantity_min'] >= $input['global_product_quantity_max'] )
            {
                $input['global_product_quantity_min'] = $input['global_product_quantity_max'] - 1;
            }
            if( empty( $input['global_total_qty_product'] ) )
            {
                $input['global_total_qty_product'] = 0;
            }
            return $input;
        }
    }
}