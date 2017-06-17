<?php
/**
 * Created by Tai.
 * Date: 28/11/2014
 * Time: 12:16 CH
 */

// Prevent hacks

if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();
// Drop table in database
global $wpdb;
$table_name = $wpdb->prefix . "wlo_order_log";
$sql = "DROP TABLE IF EXISTS $table_name;";
$wpdb->query($sql);

// Delete plugin options
delete_option( '_wlo_options' );
