<?php
if ( ! defined( 'ABSPATH' ) )
{
    exit; // Exit if accessed directly
}

/*
 * Function generate a table in databse
 * It uses for save data logs
 * @return void
 * */
function wlo_init_database()
{
    global $wpdb;
    $table_name = $wpdb->prefix . TABLE_NAME;
    //create the invitation database
    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" ) != $table_name )
    {

        $sql = "CREATE TABLE $table_name (
          id TEXT,
          order_date datetime NOT NULL default '0000-00-00 00:00:00',
          count_order INTEGER,
          cus_name TEXT,
          cus_phone TEXT,
          cus_email TEXT
        )";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}