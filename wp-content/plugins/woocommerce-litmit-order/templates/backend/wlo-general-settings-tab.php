<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if( ! class_exists('WLO_GENERAL_SETTINGS_TAB') )
{
    /**
     * Class WLO_GENERAL_SETTINGS_TAB
     * Generate general settings tab
     */
    class WLO_GENERAL_SETTINGS_TAB
    {
        /**
         * Class constructor
         * Add hooks to admin_init
         */
        function __construct()
        {
            // Register settings
            add_action( 'admin_init', array( $this, 'register_general_settings' ) );
        }

        /**
         * Add sections, fields on settings page and register settings
         * */
        function register_general_settings()
        {
            // Add section Settings
            add_settings_section( 'wlo_limit_section', __( 'Settings', 'wlo' ), array( $this, 'setting_section_callback' ), 'wlo_settings_page' );
            // Add new field to setting section
            add_settings_field( 'wlo_target_page', __( 'Target Page', 'wlo' ), array( $this, 'setting_target_page_callback' ), 'wlo_settings_page', 'wlo_limit_section' );
            add_settings_field( 'wlo_advance_limit_on_specific_days', __( 'Apply limit rules on specific days', 'wlo' ), array( $this, 'setting_field_limit_on_days_callback' ), 'wlo_settings_page', 'wlo_limit_section' );
            add_settings_field( 'wlo_advance_limit_on_next_days', __( 'Apply limit rules in date ranges', 'wlo' ), array( $this, 'setting_field_limit_on_range_days_callback' ), 'wlo_settings_page', 'wlo_limit_section' );
            add_settings_field( 'wlo_advance_limit_in_range_time', __( 'Apply limit rules in a specific time range', 'wlo' ), array( $this, 'setting_field_limit_in_range_time_callback' ), 'wlo_settings_page', 'wlo_limit_section' );
            // Register settings
            register_setting( WLO_OPION, WLO_OPION );
        }

        function setting_section_callback()
        {
            ?>

            <?php
        }

        /**
         * Add setting fields
         * Number of times customer can order and message settings
         * @return void
         * */
        function setting_field_time_order_callback()
        {
            $wlo_options = get_option( WLO_OPION );
            ?>
            <div class="wlo-rows">
                <input name="_wlo_options[limit_time]" type="number" min="0" max="100" id="limit_time" value="<?php echo $wlo_options['limit_time'] == NULL ? 5 : $wlo_options['limit_time']; ?>" class="regular-text">
                <p class="wlo-field-description"><?php _e( 'Admin can enter 0 – 100, 0 is unlimit times, Default is 5', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         * Add setting fields
         * Number of times customer can order and message settings
         * @return void
         * */
        function setting_target_page_callback()
        {
            $wlo_options = get_option( WLO_OPION );
            $link_text = isset( $wlo_options['link_text'] ) ? $wlo_options['link_text'] : '';
            $link_url = isset( $wlo_options['link_url'] ) ? $wlo_options['link_url'] : '#';
            ?>
            <div class="wlo-rows">
                <label>
                    <input name="_wlo_options[link_text]" type="text" placeholder="http://" value="<?php echo esc_attr( $link_text ); ?>" class="regular-text"><?php _e( 'Text', 'wlo' ); ?>
                </label>
            </div>
            <div class="wlo-rows">
                <label>
                    <input name="_wlo_options[link_url]" type="text" value="<?php echo esc_url( $link_url ); ?>" class="regular-text"><?php _e( 'URL', 'wlo' ); ?>
                </label>
                <p class="wlo-field-description"><?php _e( 'The page link displays next to the error message', 'wlo' ); ?></p>
            </div>
            <?php
        }

        /**
         * Display limit range days option
         */
        function setting_field_limit_on_range_days_callback()
        {
            $wlo_options = get_option( WLO_OPION );
            $enable_date_range = isset( $wlo_options['advance']['enable_range_date'] ) ? $wlo_options['advance']['enable_range_date'] : '';
            ?>
            <div class="wlo-rows multi-row">
                <label>
                    <input type="checkbox" name="_wlo_options[advance][enable_range_date]" value="yes" <?php checked( $enable_date_range, 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
            </div>
            <div class="wlo-rows">
                <textarea name="_wlo_options[advance][date_ranges]" class="wlo-range-times" rows="3"><?php echo ! isset( $wlo_options['advance']['date_ranges'] ) ? '' : $wlo_options['advance']['date_ranges']; ?></textarea>
                <p class="wlo-field-description"><?php _e( 'You can enter <strong>only one rule per line</strong> with format <strong> mm/dd/yyyy - mm/dd/yyyy </strong>. Ex: 06/12/2016 - 06/30/2016', 'wlo' ); ?></p>
            </div>
            <?php
        }
        /**
         * Advance Options on plugin
         * @return void
         */
        function setting_field_limit_on_days_callback()
        {
            $wlo_options = get_option( WLO_OPION );
            $advance_date = isset( $wlo_options['advance']['date'] ) ? $wlo_options['advance']['date'] : '';
            $advance_selected_date = isset( $wlo_options['advance']['selected_date'] ) ? $wlo_options['advance']['selected_date'] : '';
            ?>
            <div class="wlo-rows multi-row">
                <label>
                    <input type="checkbox" name="_wlo_options[advance][date]" value="yes" <?php checked( $advance_date, 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
            </div>

            <div class="wlo-advance-container">
                <div id="wlo-date-picker"></div> <input type="button" id="wlo_clear_selected_dates" title="<?php _e( 'Clear Selected Dates', 'wlo' ); ?>" value="<?php _e( 'Clear', 'wlo' ); ?>">
                <textarea name="_wlo_options[advance][selected_date]" class="wlo-selected-dates" rows="3"><?php echo $advance_selected_date; ?></textarea>
            </div>
            <?php
        }

        function setting_field_limit_in_range_time_callback()
        {
            $wlo_options = get_option( WLO_OPION );
            $advance_time = isset( $wlo_options['advance']['time'] ) ? $wlo_options['advance']['time'] : '';
            $advance_time_selected = isset( $wlo_options['advance']['time_selected'] ) ? $wlo_options['advance']['time_selected'] : '';
            ?>
            <div class="wlo-rows multi-row">
                <label>
                    <input type="checkbox" name="_wlo_options[advance][time]" value="yes" <?php checked( $advance_time, 'yes' ); ?> >
                    <?php _e( 'Enable', 'wlo' ); ?>
                </label>
            </div>

            <div class="wlo-advance-container">
                <div id="wlo-time-range">
                    <p>Time Range: <span class="wlo-slider-time"></span> - <span class="wlo-slider-time2"></span>
                    </p>
                    <div class="wlo-sliders-step1">
                        <div id="wlo-slider-range"></div>
                    </div>
                    <input type="hidden" id="wlo-time-selected" name="_wlo_options[advance][time_selected]" value="<?php echo $advance_time_selected; ?>">
                </div>
            </div>
            <?php
        }
    }
}