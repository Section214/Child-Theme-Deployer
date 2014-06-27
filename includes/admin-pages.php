<?php
/**
 * Admin Pages
 *
 * @package     ChildThemeDeployer/Admin/Pages
 * @since       1.0.0
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Navigation tabs
 *
 * @since		1.0.0
 * @return 		void
 */
function ctdeployer_tabs() {
	$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'ctdeployer';
	?>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php echo $selected == 'ctdeployer' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ctdeployer' ), 'themes.php' ) ) ); ?>">
			<?php _e( 'Build', 'child-theme-deployer' ); ?>
		</a>
	</h2>
	<?php
}


/**
 * Render CTDeployer build screen
 *
 * @since		1.0.0
 * @return		void
 */
function ctdeployer_build_screen() {
	$theme    = wp_get_theme();
	$site     = get_bloginfo( 'name' );
	$disabled = '';

	$args  = array(
		'theme_name'	=> ( isset( $_POST['ctdeployer']['theme_name'] ) ? $_POST['ctdeployer']['theme_name'] : sprintf( __( '%s - %s Child', 'child-theme-deployer' ), $site, $theme->Name ) ),
		'theme_uri'		=> ( isset( $_POST['ctdeployer']['theme_uri'] ) ? $_POST['ctdeployer']['theme_uri'] : site_url() ),
		'description'	=> ( isset( $_POST['ctdeployer']['description'] ) ? $_POST['ctdeployer']['description'] : '' ),
		'author'		=> ( isset( $_POST['ctdeployer']['author'] ) ? $_POST['ctdeployer']['author'] : '' ),
		'author_uri'	=> ( isset( $_POST['ctdeployer']['author_uri'] ) ? $_POST['ctdeployer']['author_uri'] : '' ),
		'version'		=> ( isset( $_POST['ctdeployer']['version'] ) ? $_POST['ctdeployer']['version'] : $theme->Version )
	);
	?>
	<style type="text/css" media="screen">
		/*<![CDATA[*/
		.ctdeployer-required {
			color: #ff0000;
		}
		/*]]>*/
	</style>
	<div class="wrap">
		<h2><?php _e( 'Child Theme Deployer', 'child-theme-deployer' ); ?></h2>

		<?php ctdeployer_tabs(); ?>

		<div class="metabox-holder">
			<div class="postbox">
				<h3><span><?php _e( 'Child Theme Settings', 'child-theme-deployer' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Specify the settings for your child theme. Fields left blank will be ignored.', 'child-theme-deployer' ); ?></p>
					<form method="post" action="<?php echo admin_url( 'themes.php?page=ctdeployer' ); ?>">
						<table class="form-table">
							<tbody>
								<tr>
									<th scope="row">
										<label for="ctdeployer[theme_name]"><?php _e( 'Theme Name', 'child-theme-deployer' ); ?><span class="ctdeployer-required">*</span></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[theme_name]" name="ctdeployer[theme_name]" value="<?php echo $args['theme_name']; ?>" />
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[theme_uri]"><?php _e( 'Theme URI', 'child-theme-deployer' ); ?></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[theme_uri]" name="ctdeployer[theme_uri]" value="<?php echo $args['theme_uri']; ?>" />
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[description]"><?php _e( 'Description', 'child-theme-deployer' ); ?></label>
									</th>
									<td>
										<textarea rows="6" style="width: 25em;" id="ctdeployer[description]" name="ctdeployer[description]"><?php echo $args['description']; ?></textarea>
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[author]"><?php _e( 'Author', 'child-theme-deployer' ); ?></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[author]" name="ctdeployer[author]" value="<?php echo $args['author']; ?>" />
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[author_uri]"><?php _e( 'Author URI', 'child-theme-deployer' ); ?></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[author_uri]" name="ctdeployer[author_uri]" value="<?php echo $args['author_uri']; ?>" />
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[template]"><?php _e( 'Template', 'child-theme-deployer' ); ?><span class="ctdeployer-required">*</span></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[template]" name="ctdeployer[template]" value="<?php echo $theme->template; ?>" readonly />
									</td>
								</tr>
								<tr>
									<th scope="row">
										<label for="ctdeployer[version]"><?php _e( 'Version', 'child-theme-deployer' ); ?></label>
									</th>
									<td>
										<input type="text" class="regular-text" id="ctdeployer[version]" name="ctdeployer[version]" value="<?php echo $args['version']; ?>" />
									</td>
								</tr>
							</tbody>
						</table>

						<input type="hidden" name="ctdeployer-action" value="build_child_theme" />
						<?php wp_nonce_field( 'ctdeploy_nonce', 'ctdeploy_nonce' ); ?>
						<?php if( is_child_theme() ) {
							ctdeployer_display_error( 'child_theme_active' );
						} else {
							submit_button( __( 'Build &amp; Activate', 'child-theme-deployer' ), 'secondary', 'submit', false );
						} ?>
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}


/**
 * Render CTDeployer about screen
 *
 * @since		1.0.0
 * @return		void
 */
function ctdeployer_about_screen() {
	?>
	<div class="wrap">
		<h2><?php _e( 'Child Theme Deployer', 'child-theme-deployer' ); ?></h2>

		<?php ctdeployer_tabs(); ?>
	</div>
	<?php
}