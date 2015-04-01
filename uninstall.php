<?php

// Exit if uninstall not called from WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) )  {
	exit();
}

// Delete options in database.
delete_option( 'auth_settings' );
delete_option( 'auth_settings_recently_sent_emails' );
delete_option( 'auth_settings_advanced_admin_notice' );
delete_option( 'auth_settings_advanced_login_error' );
delete_option( 'auth_settings_advanced_lockouts_time_last_failed' );
delete_option( 'auth_settings_advanced_lockouts_failed_attempts' );

// Delete multisite options
if ( is_multisite() ) {
	delete_blog_option( BLOG_ID_CURRENT_SITE, 'auth_multisite_settings' );
}

// For security, delete blocked users (since we can't enforce their blocked
// status without this plugin enabled, which means they would be able to reset
// their passwords and log in). If they have any content, reassign it to the
// current user (the user uninstalling the plugin).
if ( ! is_multisite() ) {
	$blocked_users = get_users( array(
		'meta_key' => 'auth_blocked',
		'meta_value' => 'yes',
	));
	$current_user = wp_get_current_user();
	foreach ( $blocked_users as $blocked_user ) {
		wp_delete_user( $blocked_user->ID, $current_user->ID );
	}
}
