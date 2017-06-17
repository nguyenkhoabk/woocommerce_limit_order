<?php
if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_SETUP' ) )
{
    /*
     * Class register stylesheet and js that used in plugin
     * */
	class WLO_SETUP
	{
		function __construct()
		{
			// enqueue style
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

        /*
         * Enqueues files style and scripts that used in plugin
         * @return void
         *
         * */
		function admin_scripts( $hook )
		{
            // Just enqueue on wlo setting page
            if( 'woocommerce_page_wlo_settings_page' !== $hook )
            {
                return;
            }
			// Enqueue style
			wp_enqueue_style( 'wlo-admin-style', WLO_CSS_URL . 'admin.css',array(), WLO_VERSION );
            // Enqueue select2 style
            wp_enqueue_style( 'wlo-select2-style', WLO_CSS_URL . 'select2.min.css', array(), WLO_VERSION );
			// Enqueue datepick style
			wp_enqueue_style( 'wlo-datepick-style', WLO_CSS_URL . 'jquery.datepick.css', array(), WLO_VERSION );
            // Enqueue select2 scripts
            wp_enqueue_script( 'wlo-select2-script', WLO_JS_URL . 'select2.min.js', array( 'jquery' ), WLO_VERSION, true );
			// Enqueue datepick script
			wp_enqueue_script( 'wlo-plugin-datepick-script', WLO_JS_URL . 'jquery.plugin.min.js', array( 'jquery' ), WLO_VERSION, true );
			// Enqueue datepick script
			wp_enqueue_script( 'wlo-datepick-script', WLO_JS_URL . 'jquery.datepick.min.js', array( 'jquery' ), WLO_VERSION, true );
            // Enqueue plugin script
            /** Register and enqueue scripts */
            $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script( 'wlo-scripts', WLO_JS_URL . "scripts$min.js", array( 'jquery', 'jquery-ui-slider' ), WLO_VERSION, true );

			$wlo_options = get_option( WLO_OPION );
			// Extract time selected to number
			$from_time = 0;
			$to_time = 0;
			if( isset( $wlo_options['advance']['time_selected'] ) && ! empty( $wlo_options['advance']['time_selected'] ) )
			{
				$range_time_selected = explode( '-', $wlo_options['advance']['time_selected'] );
				$from_time = trim( $range_time_selected[0] );
				$to_time = trim( $range_time_selected[1] );
			}
			$wlo_options['advance']['from_time'] = intval( $from_time ) * 60 ;
			$wlo_options['advance']['to_time'] = intval( $to_time ) * 60;
			// Localize options data
			wp_localize_script( 'wlo-scripts', 'WLO', $wlo_options );
			wp_enqueue_script( 'wlo-scripts' );
		}
	}
}