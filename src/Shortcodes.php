<?php
/**
 * Shortcodes class for Cool Kids Network.
 *
 * This class registers and handles shortcodes for rendering various templates.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcodes
 *
 * Registers and manages shortcodes for the plugin.
 */
class Shortcodes {
	/**
	 * Initialize shortcodes.
	 *
	 * @return void
	 */
	public static function init() {
		add_shortcode( 'coolkids_home', array( __CLASS__, 'render_home' ) );
		add_shortcode( 'coolkids_login', array( __CLASS__, 'render_login_form' ) );
		add_shortcode( 'coolkids_signup', array( __CLASS__, 'render_signup_form' ) );
	}

	/**
	 * Retrieve and render a template.
	 *
	 * @param string $template_name Name of the template file (without extension).
	 * @return string Rendered template content.
	 */
	private static function get_template( $template_name ) {
		$template_path = plugin_dir_path( __FILE__ ) . '../templates/' . $template_name . '.php';

		if ( file_exists( $template_path ) ) {
			ob_start();
			include $template_path;
			return ob_get_clean();
		}
		// Fallback if template is missing.
		return '';
	}

	/**
	 * Render the home view.
	 *
	 * @return string Rendered home template.
	 */
	public static function render_home() {
		$user_data = Utils::get_user_data();

		return $user_data
			? self::get_template( 'user-view', $user_data )
			: self::get_template( 'guest-view' );
	}

	/**
	 * Render the login form.
	 *
	 * @return string Rendered login template or a message if already logged in.
	 */
	public static function render_login_form() {
		if ( is_user_logged_in() ) {
			return '<p>' . sprintf(
				esc_html__( 'You are already logged in. %s', 'ckn' ),
				'<a href="' . esc_url( wp_logout_url( home_url() ) ) . '">' . esc_html__( 'Logout', 'ckn' ) . '</a>'
			) . '</p>';
		}

		return self::get_template( 'login' );
	}

	/**
	 * Render the signup form.
	 *
	 * @return string Rendered signup template.
	 */
	public static function render_signup_form() {
		return self::get_template( 'signup' );
	}
}
