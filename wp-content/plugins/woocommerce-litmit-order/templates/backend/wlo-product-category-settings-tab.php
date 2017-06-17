<?php

if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_PRODUCT_CATEGORY_SETTINGS_TAB' ) )
{
    /**
     * Class WLO_PRODUCT_CATEGORY_SETTINGS
     * add more setting fields into product taxonomy 
     */
    class WLO_PRODUCT_CATEGORY_SETTINGS_TAB
    {
        /**
         * WLO_PRODUCT_CATEGORY_SETTINGS constructor.
         */
        function __construct()
        {
            // Add form
            // Register User settings
            add_action( 'admin_init', array( $this, 'register_limit_settings' ) );
        }

        /**
         * Add sections, fields on settings page and register settings
         * */
        function register_limit_settings()
        {
            add_settings_section('wlo_settings_limit_product_cat_section', __('', 'wlo'), array( $this, 'setting_section_callback' ) , 'wlo_limit_product_cat_settings_page' );
            add_settings_field('wlo_enable_limit_product_field', __('Limit Order Type', 'wlo'), array( $this, 'setting_enable_limit_order_product_cat_callback' ) , 'wlo_limit_product_cat_settings_page', 'wlo_settings_limit_product_cat_section' );
            add_settings_field('wlo_enable_limit_product_cat_message_field', __('Message', 'wlo'), array( $this, 'setting_limit_order_product_cat_message_callback' ) , 'wlo_limit_product_cat_settings_page', 'wlo_settings_limit_product_cat_section' );
            add_settings_field('wlo_limit_product_category_rules', __('Limit Rules', 'wlo'), array( $this, 'setting_limit_product_cat_callback' ) , 'wlo_limit_product_cat_settings_page', 'wlo_settings_limit_product_cat_section' );
        }

        function setting_section_callback()
        {
            ?>
            <?php
        }

        /**
         * Dislay checkbox enable limit product category
         */
        function setting_enable_limit_order_product_cat_callback()
        {
            $_wlo_product_category_options = get_option( WLO_PRODUCT_CATEGORY_OPTION );
            $_wlo_product_category_options['limit_on_product_category'] = isset( $_wlo_product_category_options['limit_on_product_category'] ) ? $_wlo_product_category_options['limit_on_product_category'] : '' ;
            ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_product_category_options[limit_on_product_category]" value="yes" <?php checked( $_wlo_product_category_options['limit_on_product_category'], 'yes' ); ?> >
                    <?php _e( 'Enable limit order product category', 'wlo' ); ?>
                </label>
            </div>
            <?php
        }

        /**
         * Dislay limit message
         */
        function setting_limit_order_product_cat_message_callback()
        {
            $_wlo_product_category_options = get_option( WLO_PRODUCT_CATEGORY_OPTION );
            $_wlo_product_category_options['limit_product_category_message'] = isset( $_wlo_product_category_options['limit_product_category_message'] ) ? $_wlo_product_category_options['limit_product_category_message'] : __( 'You purchased total [total_number] products in [product_category] , from [from_date_range] to [to_date_range] you just can buy [limit_number] products in this category', 'wlo' ) ;
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_product_category_options[limit_product_category_message]" class="wlo-message"><?php echo $_wlo_product_category_options['limit_product_category_message']; ?></textarea>
                <p class="wlo-field-description">Use <strong>[total_number]</strong> to show total quantity of products purchased in a product category, use <strong>[product_category]</strong> to show product category name reached limit rule, <strong>[from_date_range]</strong> to show start date in date range , <strong>[to_date_range]</strong> to show end date in date range,
                use <strong>[limit_number]</strong> to show total quantity of product can purchase in a product category</p>

            </div>
            <?php
        }

        /**
         * Dislay limit product categories fields
         */
        function setting_limit_product_cat_callback()
        {
            $_wlo_product_category_options = get_option( WLO_PRODUCT_CATEGORY_OPTION );
            $total_row = 0;
            if ( isset( $_wlo_product_category_options ) )
            {
                $total_row = $_wlo_product_category_options['total-rows'];
            }
        ?>
            <div class="wlo-rows wlo-list-product-category-rules">
                <?php
                global $wp_version;
                if ( $wp_version >= 4.5 )
                {
                    $p_categories = get_terms( array( 'taxonomy' => 'product_cat' ) );
                }
                else
                {
                    $p_categories = get_terms( 'post_tag', array(
                        'hide_empty' => false,
                    ) );
                }
                ?>

                <table id="list-limit-product-category-rule" class="widefat" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="apply-rule"><?php _e( 'Apply rule', 'wlo' ); ?></th>
                            <th class="rule-name"><?php _e( 'Rule  Name', 'wlo' ); ?></th>
                            <th class="cat"><?php _e( 'Product Category', 'wlo' ); ?></th>
                            <th class="quantity"><?php _e( 'The number of product can purchase', 'wlo' ); ?></th>
                            <th class="roles"><?php _e( 'Apply to role', 'wlo' ); ?></th>
                            <th class="all-role"><?php _e( 'Apply to all roles', 'wlo' ); ?></th>
                            <th class="date-range"><?php _e( 'Apply in date range', 'wlo' ); ?></th>
                            <th class="action-button"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $current_row = 0;
                    do
                    {
                    ?>
                        <tr class="limit-rule" data-row-index="<?php echo $current_row; ?>">
                            <td class="apply-rule">
                                <input type="checkbox" class="rule-element row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][apply_rule]" value="1" <?php checked( 1, isset( $_wlo_product_category_options['row_'.$current_row]['apply_rule'] ) ? $_wlo_product_category_options['row_'.$current_row]['apply_rule'] : 0 ); ?> >
                            </td>
                            <td class="rule-name"><input class="row-element" type="text" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][rule_name]" value="<?php echo isset( $_wlo_product_category_options['row_'.$current_row]['rule_name'] ) ? $_wlo_product_category_options['row_'.$current_row]['rule_name'] : ''; ?>"></td>
                            <td class="cat">
                                <select class="wlo-list-product-cat row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][categories][]" multiple="multiple">
                                    <?php
                                    foreach ( $p_categories  as $p_category )
                                    {
                                        ?>
                                        <option <?php selected( $p_category->term_id, isset( $_wlo_product_category_options['row_'.$current_row]['categories'] ) && in_array( $p_category->term_id, $_wlo_product_category_options['row_'.$current_row]['categories'] ) ? $p_category->term_id : '' ); ?> value="<?php echo esc_attr( $p_category->term_id )?>"><?php esc_html_e( $p_category->name ); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="quantity">
                                <input type="number" min="0" step="1" class="row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][limit_number]" value="<?php echo isset( $_wlo_product_category_options['row_'.$current_row]['limit_number'] ) ? $_wlo_product_category_options['row_'.$current_row]['limit_number'] : 0; ?>">
                            </td>
                            <td class="roles">
                                <select class="wlo-list-roles row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][limit_roles][]"  multiple="multiple">
                                    <?php
                                    // get all roles
                                    $roles = get_editable_roles();
                                    foreach ( $roles as $key => $role )
                                    {
                                        ?>
                                        <option <?php selected( $key, isset( $_wlo_product_category_options['row_'.$current_row]['limit_roles'] ) && in_array( $key, $_wlo_product_category_options['row_'.$current_row]['limit_roles'] ) ? $key : '' ); ?> value="<?php echo esc_attr( $key )?>"><?php esc_html_e( $role['name'] ); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="all-role">
                                <input type="checkbox" class="row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][limit_all_roles]" value="1" <?php checked( 1, isset( $_wlo_product_category_options['row_'.$current_row]['limit_all_roles'] ) ? $_wlo_product_category_options['row_'.$current_row]['limit_all_roles'] : 0 ); ?> >
                            </td>
                            <td class="date-range">
                                <textarea class="row-element" name="_wlo_product_category_options[row_<?php echo  $current_row; ?>][date_range]" rows="3"><?php echo isset( $_wlo_product_category_options['row_'.$current_row]['date_range'] ) ? $_wlo_product_category_options['row_'.$current_row]['date_range'] : ''; ?></textarea>
                                <p class="wlo-field-description"><strong>only one rule per line</strong> with format <strong> mm/dd/yyyy - mm/dd/yyyy </strong>
                            </td>
                            <td class="action-button"><button type="button" class="btn btn-action <?php echo 0 == $current_row ? 'btn-add-rule' : '-'; ?>"><?php echo 0 == $current_row ? '+' : '-' ?></button></td>
                        </tr>
                    <?php
                        $current_row++;
                    }
                    while( $current_row < $total_row );
                    ?>

                    </tbody>
                </table>
                <input type="hidden" class="total-rows" name="_wlo_product_category_options[total-rows]" value="<?php echo isset( $_wlo_product_category_options['total-rows'] ) ? $_wlo_product_category_options['total-rows'] : 1; ?>">
            </div><!-- end .wlo-list-product-category-rules -->
        <?php
        }
    }
}