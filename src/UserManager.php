<?php
/**
 * UserManager Class
 *
 * Handles user signup, login, redirections, and admin bar visibility.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class UserManager
 *
 * Provides functionality for user authentication, login handling,
 * redirection of logged-in users, and hiding the admin bar for all cookid roles.
 */
class UserManager {
	/**
	 * Initialize hooks for user management.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'handle_signup' ) );
		add_action( 'init', array( __CLASS__, 'handle_login' ) );
		add_action( 'template_redirect', array( __CLASS__, 'redirect_logged_in_users' ) );
		add_filter( 'show_admin_bar', array( __CLASS__, 'remove_admin_bar_for_coolkid_roles' ) );
	}

	/**
	 * Handles user signup process.
	 *
	 * Verifies nonce, sanitizes input, and processes signup authentication.
	 * Redirects accordingly upon success or failure.
	 *
	 * @return void
	 */
	public static function handle_signup() {
		if ( isset( $_POST['coolkids_signup'] ) && ! empty( $_POST['email'] ) ) {
			check_admin_referer( 'coolkids_signup', 'coolkids_nonce' );

			$email = sanitize_email( wp_unslash( $_POST['email'] ) );

			$result = Utils::authenticate_email( sanitize_email( wp_unslash( $_POST['email'] ) ) );

			if ( is_wp_error( $result ) ) {
				set_transient( 'coolkids_signup_failed', $result->get_error_message(), 30 );
				wp_safe_redirect( home_url( '/signup/' ) );
				exit();
			}

			set_transient( 'coolkids_signup_success', true, 30 );
			wp_safe_redirect( home_url( '/signup/' ) );
			exit();
		}
	}

	/**
	 * Handles user login process.
	 *
	 * Verifies nonce and processes user authentication.
	 *
	 * @return void
	 */
	public static function handle_login() {
		if ( isset( $_POST['coolkids_login'] ) && ! empty( $_POST['email'] ) ) {
			check_admin_referer( 'coolkids_login', 'coolkids_nonce' );

			Utils::authenticate_user( sanitize_email( wp_unslash( $_POST['email'] ) ) );
		}
	}

	/**
	 * Redirects logged-in users away from signup and login pages.
	 *
	 * @return void
	 */
	public static function redirect_logged_in_users() {
		if ( is_user_logged_in() ) {
			$restricted_pages = array( '/signup', '/login' );
			$current_url      = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

			if ( in_array( $current_url, $restricted_pages, true ) ) {
				wp_safe_redirect( home_url( '/' ) );
				exit;
			}
		}
	}
	/**
	 * Hides the admin bar for coolkid user roles.
	 *
	 * @param bool $show_admin_bar Whether to show the admin bar.
	 * @return bool Filtered value determining admin bar visibility.
	 */
	public static function remove_admin_bar_for_coolkid_roles( $show_admin_bar ) {
		$user = wp_get_current_user();

		if ( empty( $user->roles ) ) {
			return $show_admin_bar;
		}

		$roles_to_remove = array( 'cool_kid', 'cooler_kid', 'coolest_kid' );

		return ! array_intersect( $roles_to_remove, $user->roles );
	}
}
