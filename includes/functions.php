<?php
/**
 * Misc Functions
 *
 * @package     ChildThemeDeployer/Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display errors
 *
 * @since       1.0.0
 * @param       string $error The error type to display
 * @return      void
 */
function ctdeployer_display_error( $error ) {

    $errors = array(
        'missing_theme_name'       => __( 'Theme name is required!', 'child-theme-deployer' ),
        'directory_exists'         => sprintf( __( 'Theme directory exists! Please <a href="%s">click here</a> to activate the existing theme.', 'child-theme-deployer' ), add_query_arg( array( 'ctdeployer-action' => 'activate_child_theme' ) ) ),
        'directory_create_fail'    => __( 'Unable to create theme directory! Please check the directory permissions and try again.', 'child-theme-deployer' ),
        'child_theme_active'       => __( 'You shouldn\'t be here... you already have a child theme active!', 'child-theme-deployer' ),
    );

    if( array_key_exists( $error, $errors ) ) {
        echo '<div class="error"><p>' . $errors[$error] . '</p></div>';
    } else {
        echo '<div class="error"><p>' . __( 'An unknown error occurred!', 'child-theme-deployer' ) . '</p></div>';
    }
}