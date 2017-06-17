<?php
/**
 * Settings Page
 */

if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WLO_SETTINGS_PAGE' ) )
{

    /**
     *  Class add setting page to woocommerce menu
     * */

	class WLO_SETTINGS_PAGE
	{
        /**
         * Class constructor
         * Add hooks to Woocommerce menu and register settings
         */
		function __construct()
		{
			add_action( 'admin_menu', array( $this, 'add_sub_menu' ) );
            // Register settings
            add_action( 'admin_init', array( $this, 'register_setting_option' ) );
            // include tabs setting
            $this->require_tabs_setting();
		}

        /**
         * Require tabs use for plugin
         */
        function require_tabs_setting()
        {
            require WLO_TEMPLATES_BACKEND_PATH . 'wlo-general-settings-tab.php';
            new WLO_GENERAL_SETTINGS_TAB();
            require WLO_TEMPLATES_BACKEND_PATH . 'wlo-user-settings-tab.php';
            new  WLO_USER_SETTINGS_TAB();
            require WLO_TEMPLATES_BACKEND_PATH . 'wlo-product-settings-tab.php';
            new  WLO_PRODUCT_SETTINGS_TAB();
            require WLO_TEMPLATES_BACKEND_PATH . 'wlo-product-category-settings-tab.php';
            new  WLO_PRODUCT_CATEGORY_SETTINGS_TAB();
        }
        /**
         * Register setting option
         */
        function register_setting_option()
        {
            register_setting( WLO_OPION, WLO_OPION );
            register_setting( WLO_USER_OPION, WLO_USER_OPION );
            register_setting( WLO_PRODUCT_OPION, WLO_PRODUCT_OPION, array( 'WLO_PRODUCT_SETTINGS_TAB', 'sanitize_product_settings' ) );
            register_setting( WLO_PRODUCT_CATEGORY_OPTION, WLO_PRODUCT_CATEGORY_OPTION );
        }

        /**
         * Add setting page sub menu to woocommerce menu
         * @return void
         * */
		function  add_sub_menu()
		{
			add_submenu_page(
				'woocommerce',
				__( 'Limit Order', 'wlo' ),
				__( 'Limit Order', 'wlo' ),
				'manage_woocommerce',
				'wlo_settings_page',
				array( $this, 'settings_page' )
			);
		}

        /**
         * The range time need to get order log
         */
        function filter_nav()
        {
            $active = isset( $_GET['s_filter'] ) ? $_GET['s_filter'] : 'today';
            ?>
            <ul class="subsubsub wlo-statistic-nav">
                <li><a href="?page=wlo_settings_page&tab=statistic&s_filter=today" class="<?php echo 'today' == $active? 'current' : ''?>"><?php _e( 'Today', 'wlo' ); ?></a> | </li>
                <li><a href="?page=wlo_settings_page&tab=statistic&s_filter=one_week" class="<?php echo 'one_week' == $active? 'current' : ''?>"><?php _e( 'One Week', 'wlo' ); ?></a> | </li>
                <li><a href="?page=wlo_settings_page&tab=statistic&s_filter=one_month" class="<?php echo 'one_month' == $active? 'current' : ''?>"><?php _e( 'One Month', 'wlo' ); ?></a> | </li>
                <li><a href="?page=wlo_settings_page&tab=statistic&s_filter=one_year" class="<?php echo 'one_year' == $active? 'current' : ''?>"><?php _e( 'One Year', 'wlo' ); ?></a></li>
            </ul>
        <?php
        }

        /**
         * Display form setting
         * @return void
         * */
		function settings_page()
		{
		?>
            <?php settings_errors(); ?>
            <div class="wrap-header">
                <?php
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings';
                $tabs = array(
                    'customers'      =>  __( 'Customers', 'wlo' ),
                    'products'   =>  __( 'Products', 'wlo' ),
                    'product_category'   =>  __( 'Products Category', 'wlo' ),
                    'settings'   =>  __( 'Advance', 'wlo' ),
                );
                ?>
                <h2 class="nav-tab-wrapper">
                <?php
                foreach( $tabs as $key => $tab )
                {
                ?>
                    <a href="?page=wlo_settings_page&tab=<?php echo $key; ?>" class="nav-tab <?php echo $active_tab == $key ? 'nav-tab-active' : ''; ?>"><?php _e( $tab, 'wlo' ); ?></a>
                <?php
                }
                ?>
                </h2>
            </div>
			<form class="mlo-container" method="post" action="options.php">
            <?php
                if( 'settings' === $active_tab )
                {
                    settings_fields(WLO_OPION);
                    do_settings_sections('wlo_settings_page');
                }
                elseif ( 'customers' === $active_tab )
                {
                    settings_fields(WLO_USER_OPION);
                    do_settings_sections('wlo_limit_user_settings_page');
                }
                elseif ( 'products' === $active_tab )
                {
                    settings_fields( WLO_PRODUCT_OPION );
                    do_settings_sections('wlo_limit_product_settings_page');
                }
                elseif ( 'product_category' === $active_tab )
                {
                    settings_fields( WLO_PRODUCT_CATEGORY_OPTION );
                    do_settings_sections('wlo_limit_product_cat_settings_page');
                }
                submit_button();
            ?>
			</form>
		<?php
		}
	}
}
