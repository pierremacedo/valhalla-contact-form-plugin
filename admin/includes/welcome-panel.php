<?php

function wpcf7_welcome_panel() {
	$classes = 'welcome-panel';

	$vers = (array) get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true );

	if ( wpcf7_version_grep( wpcf7_version( 'only_major=1' ), $vers ) ) {
		$classes .= ' hidden';
	}

?>

<?php
}

add_action( 'wp_ajax_wpcf7-update-welcome-panel',
	'wpcf7_admin_ajax_welcome_panel', 10, 0 );

function wpcf7_admin_ajax_welcome_panel() {
	check_ajax_referer( 'wpcf7-welcome-panel-nonce', 'welcomepanelnonce' );

	$vers = get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true );

	if ( empty( $vers ) or ! is_array( $vers ) ) {
		$vers = array();
	}

	if ( empty( $_POST['visible'] ) ) {
		$vers[] = wpcf7_version( 'only_major=1' );
	}

	$vers = array_unique( $vers );

	update_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', $vers );

	wp_die( 1 );
}
