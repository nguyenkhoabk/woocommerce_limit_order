<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_USER_SETTINGS_TAB') )
{
    /**
     * Class WLO_USER_SETTINGS_TAB
     * Generate user settings tab
     */
    class WLO_USER_SETTINGS_TAB
    {
        /**
         * WLO_USER_SETTINGS_TAB constructor.
         * Add hooks to Woocommerce menu and register settings
         */
        function __construct()
        {
            // Register User settings
            add_action( 'admin_init', array( $this, 'register_limit_user_settings' ) );
        }

        /**
         * Add sections, fields on settings page and register settings
         * */
        function register_limit_user_settings()
        {
            // Add new section
            add_settings_section('wlo_settings_limit_user_section', __('', 'wlo'), array( $this, 'setting_section_callback' ) , 'wlo_limit_user_settings_page' );
            add_settings_field('wlo_enable_limit_user_field', __('Limit Order Type', 'wlo'), array( $this, 'setting_enable_limt_order_customer_callback' ) , 'wlo_limit_user_settings_page', 'wlo_settings_limit_user_section' );
            add_settings_field('wlo_time_limit_user_field', __('Number Of Times Customer Can Order', 'wlo'), array( $this, 'setting_field_time_order_callback' ) , 'wlo_limit_user_settings_page', 'wlo_settings_limit_user_section' );
            add_settings_field( 'wlo_error_message_fields', __( 'Order quantity limit reached Message', 'wlo' ), array( $this, 'setting_error_message_field_callback' ), 'wlo_limit_user_settings_page', 'wlo_settings_limit_user_section' );
            add_settings_field('wlo_limit_users_fields', __('Apply Limit To Customer Types', 'wlo'), array( $this, 'setting_field_apply_for_user_callback' ) , 'wlo_limit_user_settings_page', 'wlo_settings_limit_user_section' );
        }

        function setting_section_callback()
        {
            ?>
            <?php
        }

        /**
         * Dislay checkbox enable limit order on customer
         */
        function setting_enable_limt_order_customer_callback()
        {
            $wlo_user_options = get_option( WLO_USER_OPION );
            $limit_on_identity = isset( $wlo_user_options['limit_on_identity'] ) ? $wlo_user_options['limit_on_identity'] : '';
        ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_user_options[limit_on_identity]" value="yes" <?php checked( $limit_on_identity, 'yes' ); ?> >
                    <?php _e( 'Enable limit customer order', 'wlo' ); ?>
                </label>
            </div>
        <?php
        }

        /**
         * Add setting fields
         * Number of times customer can order and message settings
         * @return void
         * */
        function setting_field_time_order_callback()
        {
            $wlo_user_options = get_option( WLO_USER_OPION );
            ?>
            <div class="wlo-rows">
                <input name="_wlo_user_options[limit_time_for_customer]" type="number" min="0" max="100" id="limit_time" value="<?php echo $wlo_user_options['limit_time_for_customer'] == NULL ? 5 : $wlo_user_options['limit_time_for_customer']; ?>" class="regular-text">
                <p class="wlo-field-description"><?php _e( 'Admin can enter 0 – 100, 0 is unlimit times, Default is 5', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         * Add setting fields
         * Number of times customer can order and message settings
         * @return void
         * */
        function setting_error_message_field_callback()
        {
            $wlo_user_options = get_option( WLO_USER_OPION );
            ?>
            <div class="wlo-rows">
                <textarea name="_wlo_user_options[message]" class="wlo-message" class="regular-text"><?php echo $wlo_user_options['message'] == NULL ? __( 'You have exceeded the allowed order time today. The number of times you can order is [number_times]', 'wlo' ) : $wlo_user_options['message']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'The message displays when customer exceed the allowed order time, use <strong>[number_times]</strong> to display number of times can order', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         * Add setting fields
         * Apply limit order for instances settings
         * @return void
         * */
        function setting_field_apply_for_user_callback()
        {
            $wlo_user_options = get_option( WLO_USER_OPION );
            $apply_for_all = isset( $wlo_user_options['apply_for_all'] ) ? $wlo_user_options['apply_for_all'] : '';
            $apply_for_guest = isset( $wlo_user_options['apply_for_guest'] ) ? $wlo_user_options['apply_for_guest'] : '';
            $apply_for_users = isset( $wlo_user_options['apply_for_users'] ) ? $wlo_user_options['apply_for_users'] : '';
            ?>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_user_options[apply_for_all]" value="yes" <?php checked( $apply_for_all, 'yes' ); ?> >
                    <?php _e( 'All Customers', 'wlo' ); ?>
                </label>
            </div>
            <p class="wlo-field-description">
                <?php _e( 'Limit order by total transactions processed in the allowed order time of all customers, include types: Guest, Members and Roles', 'wlo' ); ?>
            </p>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_user_options[apply_for_guest]" value="yes" <?php checked( $apply_for_guest, 'yes' ); ?> >
                    <?php _e( 'Guest', 'wlo' ); ?>
                </label>
            </div>
            <p class="wlo-field-description">
                <?php _e( 'Limit order applies for guests who do not login and they are limited by IP address', 'wlo' ); ?>
            </p>
            <div class="wlo-rows">
                <label>
                    <input type="checkbox" name="_wlo_user_options[apply_for_users]" value="yes" <?php checked( $apply_for_users, 'yes' ); ?> >
                    <?php _e( 'Member', 'wlo' ); ?>
                </label>
            </div>
            <div class="wlo-rows wlo-list-users-container">
                <?php
                $all_users = get_users();
                $list_roles_selected = array();
                if( isset( $wlo_user_options['limit_users'] ) )
                {
                    $list_roles_selected = $wlo_user_options['limit_users'];
                }
                ?>
                <select class="wlo-list-users form-control" name="_wlo_user_options[limit_users][]" multiple="multiple" style="width: 100%">
                    <?php
                    foreach( $all_users as $user )
                    {
                        $user_selected = '';
                        if( in_array( $user->ID, $list_roles_selected ) )
                        {
                            $user_selected = $user->ID;
                        }
                        ?>
                        <option <?php selected( $user->ID, $user_selected ); ?> value="<?php echo $user->ID; ?>"><?php echo $user->user_login; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="wlo-rows">
                <?php
                if( ! isset($wlo_user_options['apply_for_roles']) )
                {
                    $wlo_user_options['apply_for_roles'] = 'no';
                }
                ?>
                <p>
                    <label>
                        <input type="radio" name="_wlo_user_options[apply_for_roles]" value="no" <?php checked( $wlo_user_options['apply_for_roles'], 'no' ); ?> >
                        <?php _e( 'No limit role', 'wlo' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="radio" name="_wlo_user_options[apply_for_roles]" value="all" <?php checked( $wlo_user_options['apply_for_roles'], 'all' ); ?> >
                        <?php _e( 'All roles', 'wlo' ); ?>
                    </label>
                </p>
                <p>
                    <label>
                        <input type="radio" name="_wlo_user_options[apply_for_roles]" value="specific" <?php checked( $wlo_user_options['apply_for_roles'], 'specific' ); ?> />
                        <?php _e( 'Specific role', 'wlo' ); ?>
                    </label>
                </p>
            </div>
            <div class="wlo-rows wlo-list-roles-container">
                <?php
                // get all roles
                $roles = get_editable_roles();
                ?>
                    <table class="widefat wlo-roles-table" cellpadding="0">
                        <thead>
                            <th class="select-role"></th>
                            <th class="name"><?php _e( 'Role', 'wlo' ); ?></th>
                            <th class="number-order"><?php _e( 'Number of times can order' ); ?></th>
                        </thead>
                        <tbody>
                        <?php
                        foreach ( $roles as $key => $role )
                        {
                            $selected = 'no';
                            if( isset( $wlo_user_options['list_role'] ) )
                            {
                                if( in_array( $key, $wlo_user_options['list_role'] ) )
                                {
                                    $selected = 'yes';
                                }
                            }

                        ?>
                            <tr>
                                <td><input type="checkbox" name="_wlo_user_options[list_role][]" id="role-<?php echo $key; ?>" value="<?php echo $key; ?>" <?php checked( 'yes', $selected); ?> ></td>
                                <td class="name"><label for="role-<?php echo $key; ?>"><?php echo $role['name']; ?></label></td>
                                <td class="number-times">
                                    <input name="_wlo_user_options[role_time_number][<?php echo $key; ?>]" type="number" min="0" max="100" value="<?php echo empty( $wlo_user_options['role_time_number'][$key] ) ? 0 : $wlo_user_options['role_time_number'][$key] ; ?>">
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
            </div>
            <?php
        }
    }
}