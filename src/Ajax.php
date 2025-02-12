<?php
/**
 * Ajax Handler Class
 *
 * Handles AJAX requests for loading more users in the Cool Kids Network.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class Ajax
 *
 * Handles AJAX functionalities for loading more users.
 */
class Ajax {
	/**
	 * Initialize AJAX actions.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_ajax_load_more_users', array( __CLASS__, 'load_more_users' ) );
		add_action( 'wp_ajax_nopriv_load_more_users', array( __CLASS__, 'load_more_users' ) );
	}

	/**
	 * Load more users via AJAX.
	 *
	 * Checks user permissions, fetches the next set of users, and returns the HTML output.
	 *
	 * @return void Sends JSON response.
	 */
	public static function load_more_users() {
		check_ajax_referer( 'load_more_users', 'nonce' );

		$offset       = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		$current_user = wp_get_current_user();
		$user_role    = $current_user->roles[0] ?? '';

		if ( ! in_array( $user_role, array( 'cooler_kid', 'coolest_kid' ), true ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ), 403 );
		}

		$args = array(
			'number'   => 12,
			'offset'   => $offset,
			'orderby'  => 'registered',
			'order'    => 'DESC',
			'role__in' => array( 'cool_kid', 'cooler_kid', 'coolest_kid' ),
			'exclude'  => array( $current_user->ID ),
		);

		$users = get_users( $args );
		if ( empty( $users ) ) {
			wp_send_json_error( array( 'message' => 'No more users found' ) );
		}

		ob_start();
		foreach ( $users as $user_obj ) {
			echo wp_kses_post( Utils::generate_user_card_html( $user_obj, esc_attr( $user_role ) ) );
		}
		$html = ob_get_clean();

		$has_more = count( $users ) === 12;
		wp_send_json_success(
			array(
				'html'     => $html,
				'has_more' => $has_more,
			)
		);
	}
}
