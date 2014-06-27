<?php
/**
 * Admin Actions
 *
 * @package     ChildThemeDeployer/Admin/Actions
 * @since       1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Processes all CTD actions sent via POST and GET by looking for the 'ctdeployer-action'
 * request and running do_action() to call the function
 *
 * @since		1.0.0
 * @return 		void
 */
function ctdeployer_process_actions() {
	if( isset( $_POST['ctdeployer-action'] ) ) {
		do_action( 'ctdeployer_' . $_POST['ctdeployer-action'], $_POST );
	}

	if( isset( $_GET['ctdeployer-action'] ) ) {
		do_action( 'ctdeployer_' . $_GET['ctdeployer-action'], $_GET );
	}
}
add_action( 'admin_init', 'ctdeployer_process_actions' );


/**
 * Build child theme
 *
 * @since		1.0.0
 * @return		void
 */
function ctdeployer_build_child_theme() {
	if( !wp_verify_nonce( $_POST['ctdeploy_nonce'], 'ctdeploy_nonce' ) ) {
		return;
	}

	// Return if the ctdeployer arg isn't set... this shouldn't happen!
	if( !isset( $_POST['ctdeployer'] ) || empty( $_POST['ctdeployer'] ) ) {
		return;
	}

	// Unset empty fields
	foreach( $_POST['ctdeployer'] as $field => $data ) {
		if( empty( $data ) ) {
			unset( $_POST['ctdeployer'][$field] );
		}
	}

	// Bail if theme name isn't set
	if( !isset( $_POST['ctdeployer']['theme_name'] ) ) {
		ctdeployer_display_error( 'missing_theme_name' );
		return;
	}

	$site  = sanitize_title( get_bloginfo( 'name' ) );
    $theme = get_stylesheet();
    $name  = $site . '-' . $theme . '-child';

	$theme_root = trailingslashit( get_theme_root() );

	// Bail if the template directory already exists
	// (at this point it REALLY shouldn't)
	if( is_dir( $theme_root . $name ) ) {
		ctdeployer_display_error( 'directory_exists' );
		return;
	}

	// Make the new child theme directory
	if( !wp_mkdir_p( $theme_root . $name ) ) {
		ctdeployer_display_error( 'directory_create_fail' );
		return;
	}

	// Setup header
	$file_contents  = '/**' . PHP_EOL . ' * Theme Name:   ' . $_POST['ctdeployer']['theme_name'] . PHP_EOL;
	if( $_POST['ctdeployer']['theme_uri'] ) {
		$file_contents .= ' * Theme URI:    ' . $_POST['ctdeployer']['theme_uri'] . PHP_EOL;
	}
	if( $_POST['ctdeployer']['description'] ) {
 		$file_contents .= ' * Description:  ' . $_POST['ctdeployer']['description'] . PHP_EOL;
 	}
 	if( $_POST['ctdeployer']['author'] ) {
 		$file_contents .= ' * Author:       ' . $_POST['ctdeployer']['author'] . PHP_EOL;
 	}
 	if( $_POST['ctdeployer']['author_uri'] ) {
 		$file_contents .= ' * Author URI:   ' . $_POST['ctdeployer']['author_uri'] . PHP_EOL;
 	}
 	$file_contents .= ' * Template:     ' . $_POST['ctdeployer']['template'] . PHP_EOL;
 	if( $_POST['ctdeployer']['version'] ) {
 		$file_contents .= ' * Version:      ' . $_POST['ctdeployer']['version'] . PHP_EOL;
 	}
 	$file_contents .= ' */' . PHP_EOL . PHP_EOL;

 	// Import parent stylesheet
	$file_contents .= '@import url("../' . $_POST['ctdeployer']['template'] . '/style.css");' . PHP_EOL . PHP_EOL;

	$file_contents .= '/* Theme customization starts here' . PHP_EOL . '-------------------------------------------------------------- */' . PHP_EOL;

	@file_put_contents( $theme_root . $name . '/style.css', stripslashes( $file_contents ) );

	unset( $_POST );

	do_action( 'ctdeployer_activate_child_theme' );
}
add_action( 'ctdeployer_build_child_theme', 'ctdeployer_build_child_theme' );


/**
 * Activate child theme
 *
 * @since		1.0.0
 * @return		void
 */
function ctdeployer_activate_child_theme() {
	$site  = sanitize_title( get_bloginfo( 'name' ) );
    $theme = get_stylesheet();
    $name  = $site . '-' . $theme . '-child';

    switch_theme( $name );

	// Redirect to themes page
	wp_safe_redirect( remove_query_arg( array( 'ctdeployer-action', 'page' ) ) );
	exit;
}
add_action( 'ctdeployer_activate_child_theme', 'ctdeployer_activate_child_theme' );