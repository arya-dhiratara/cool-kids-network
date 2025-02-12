<?php
/**
 * API Class for Cool Kids Network
 *
 * Handles REST API routes and functionality for user role management.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class API
 *
 * Handles the registration of REST API endpoints, user role updates, and permission checks.
 */
class API {
	/**
	 * Initializes the API by registering routes.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
	}

	/**
	 * Registers the REST API routes.
	 *
	 * @return void
	 */
	public static function register_routes() {
		register_rest_route(
			'coolkids/v1',
			'/update-role/',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'update_user_roles' ),
				'permission_callback' => array( __CLASS__, 'check_permissions' ),
			)
		);
	}

	/**
	 * Updates user roles based on provided identifiers.
	 *
	 * This method searches for users by email or user meta and updates their role
	 * if a valid match is found.
	 *
	 * @param \WP_REST_Request $request The REST API request object containing user data.
	 *
	 * @return array|\WP_Error Response data or an error object.
	 */
	public static function update_user_roles( $request ) {
		$params = $request->get_json_params();
		$users  = $params['users'] ?? array();

		if ( ! is_array( $users ) || empty( $users ) ) {
			return new \WP_Error( 'missing_data', 'Users array is required.', array( 'status' => 400 ) );
		}

		// Allowed roles.
		$allowed_roles = array( 'cool_kid', 'cooler_kid', 'coolest_kid' );
		$results       = array();

		foreach ( $users as $user_data ) {
			$identifier = sanitize_text_field( $user_data['identifier'] ?? '' );
			$new_role   = sanitize_text_field( $user_data['role'] ?? '' );

			if ( ! $identifier || ! $new_role ) {
				$results[] = array(
					'identifier' => $identifier,
					'success'    => false,
					'message'    => 'Missing identifier or role.',
				);
				continue;
			}

			if ( ! in_array( $new_role, $allowed_roles, true ) ) {
				$results[] = array(
					'identifier' => $identifier,
					'success'    => false,
					'message'    => 'Invalid role specified.',
				);
				continue;
			}

			// Try finding user by email first.
			$user = get_user_by( 'email', $identifier );

			if ( ! $user ) {
				global $wpdb;

				// Fetch all users with the 'cool_kids_character' meta_key (cached for performance).
				$cache_key  = 'cool_kids_character_users';
				$meta_users = wp_cache_get( $cache_key );

				if ( false === $meta_users ) {
					$meta_users = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
						$wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
							"SELECT DISTINCT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s LIMIT 50",
							'cool_kids_character'
						)
					);

					if ( ! empty( $meta_users ) ) {
						wp_cache_set( $cache_key, $meta_users, '', 300 );
					}
				}

				// Process users.
				foreach ( $meta_users as $user_id ) {
					$character_data = get_user_meta( $user_id, 'cool_kids_character', true );

					if ( is_array( $character_data ) ) {
						$match = false;

						// Match using email.
						if ( isset( $character_data['email'] ) && $character_data['email'] === $identifier ) {
							$match = true;
						}

						// Match using first and last name.
						if ( isset( $character_data['first_name'], $character_data['last_name'] ) ) {
							$full_name = trim( $character_data['first_name'] . ' ' . $character_data['last_name'] );

							if ( $full_name === $identifier || $character_data['first_name'] === $identifier || $character_data['last_name'] === $identifier ) {
								$match = true;
							}
						}

						if ( $match ) {
							$user = get_user_by( 'id', $user_id );
							break;
						}
					}
				}
			}

			if ( ! $user ) {
				$results[] = array(
					'identifier' => $identifier,
					'success'    => false,
					'message'    => 'User not found.',
				);
				continue;
			}

			// Update user role.
			$user->set_role( $new_role );
			$results[] = array(
				'identifier' => $identifier,
				'success'    => true,
				'message'    => 'User role updated successfully.',
			);
		}

		return array( 'results' => $results );
	}


	/**
	 * Checks if the current user has permission to modify user roles.
	 *
	 * Only users with the 'manage_options' capability can modify roles
	 * using this API.
	 *
	 * @return bool True if the user has permission, false otherwise.
	 */
	public static function check_permissions() {
		return current_user_can( 'manage_options' );
	}
}
