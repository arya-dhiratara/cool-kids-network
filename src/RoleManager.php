<?php
/**
 * Role Manager Class
 *
 * Registers custom user roles for the Cool Kids Network.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class RoleManager
 *
 * Handles the registration of custom user roles.
 */
class RoleManager {
	/**
	 * Initialize role registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_roles' ) );
	}

	/**
	 * Register custom user roles.
	 *
	 * Adds the 'Cool Kid', 'Cooler Kid', and 'Coolest Kid' roles with specific capabilities.
	 *
	 * @return void
	 */
	public static function register_roles() {
		add_role(
			'cool_kid',
			'Cool Kid',
			array(
				'read' => true,
			)
		);

		add_role(
			'cooler_kid',
			'Cooler Kid',
			array(
				'read'       => true,
				'list_users' => true,
			)
		);

		add_role(
			'coolest_kid',
			'Coolest Kid',
			array(
				'read'       => true,
				'list_users' => true,
			)
		);
	}
}
