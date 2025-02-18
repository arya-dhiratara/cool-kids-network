<?php
/**
 * Utils Class
 *
 * Utility class for the Cool Kids Network plugin.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class Utils
 *
 * Provides various utility functions used across the plugin.
 */
class Utils {

	/**
	 * Generates the signup form HTML.
	 *
	 * @return void
	 */
	public static function generate_signup_form_html() {
		?>
		<form class="coolkids-form is-flex is-flex-column has-small-global-gap has-small-margin-top m-t-auto-desktop" method="post" action="">
			<?php wp_nonce_field( 'coolkids_signup', 'coolkids_nonce' ); ?>
			<label for="email"><?php esc_html_e( 'Email Address:', 'ckn' ); ?></label>
			<input type="email" name="email" class="has-square-rounded-radius" required>
			<button type="submit" name="coolkids_signup" class="ckn-button ckn-button-padding has-square-rounded-radius with-bg-color"><?php esc_html_e( 'Confirm', 'ckn' ); ?></button>
		</form>
		<p class="text-is-small has-very-small-margin-top"><?php esc_html_e( 'Have an account? Log in', 'ckn' ); ?> <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>"><?php esc_html_e( 'here', 'ckn' ); ?></a>!</p>
		<?php
	}

	/**
	 * Generates the login form HTML.
	 *
	 * @return void
	 */
	public static function generate_login_form_html() {
		?>
		<form class="coolkids-form is-flex is-flex-column has-small-global-gap has-small-margin-top m-t-auto-desktop" method="post" action="">
			<?php wp_nonce_field( 'coolkids_login', 'coolkids_nonce' ); ?>
			<label for="email"><?php esc_html_e( 'Email Address:', 'ckn' ); ?></label>
			<input type="email" name="email" class="has-square-rounded-radius" required>
			<button type="submit" name="coolkids_login" class="ckn-button ckn-button-padding has-square-rounded-radius with-bg-color"><?php esc_html_e( 'Login', 'ckn' ); ?></button>
		</form>
		<p class="text-is-small has-very-small-margin-top"><?php esc_html_e( "Don't have an account yet? Signup", 'ckn' ); ?> <a href="<?php echo esc_url( home_url( '/signup/' ) ); ?>"><?php esc_html_e( 'here', 'ckn' ); ?></a>!</p>
		<?php
	}

	/**
	 * Generates a random character for the user using randomuser API.
	 *
	 * @param string $email The email of the user.
	 * @param string $role  The role of the user.
	 * @return array Character data.
	 */
	public static function generate_character( $email, $role ) {
		$response = wp_remote_get( 'https://randomuser.me/api' );

		if ( is_wp_error( $response ) ) {
			self::log_error( 'Error fetching character: ' . $response->get_error_message() );
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( json_last_error() !== JSON_ERROR_NONE || empty( $data['results'][0] ) ) {
			self::log_error( 'Invalid JSON response from randomuser.me' );
			return array();
		}

		$user = $data['results'][0];

		return array(
			'first_name' => sanitize_text_field( $user['name']['first'] ),
			'last_name'  => sanitize_text_field( $user['name']['last'] ),
			'country'    => sanitize_text_field( $user['location']['country'] ),
			'email'      => sanitize_email( $email ),
			'role'       => sanitize_text_field( $role ),
		);
	}

	/**
	 * Authenticates email address.
	 *
	 * @param string $email The email to authenticate.
	 * @return int|\WP_Error User ID or error message.
	 */
	public static function authenticate_email( $email ) {
		$email = sanitize_email( $email );

		if ( ! is_email( $email ) ) {
			return new \WP_Error( 'invalid_email', __( 'Oops! Invalid email. Please try again.', 'ckn' ) );
		}

		if ( email_exists( $email ) ) {
			return new \WP_Error( 'email_exists', __( 'This email is already registered.', 'ckn' ) );
		}

		return self::create_user( $email, 'cool_kid' );
	}

	/**
	 * Creates new user.
	 *
	 * @param string $email The email of the new user.
	 * @param string $role  The role of the new user.
	 * @return int|\WP_Error User ID or error.
	 */
	public static function create_user( $email, $role ) {
		$email = sanitize_email( $email );

		$user_id = wp_create_user( $email, wp_generate_password(), $email );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Update role.
		wp_update_user(
			array(
				'ID'   => $user_id,
				'role' => $role,
			)
		);

		// Generate character data.
		$character = self::generate_character( $email, ucfirst( str_replace( '_', ' ', $role ) ) );

		if ( ! empty( $character ) ) {
			// Store individual user meta fields.
			update_user_meta( $user_id, 'first_name', $character['first_name'] );
			update_user_meta( $user_id, 'last_name', $character['last_name'] );
			update_user_meta( $user_id, 'country', $character['country'] );
			update_user_meta( $user_id, 'cool_kids_role', $character['role'] );

			// Store the full character object.
			update_user_meta( $user_id, 'cool_kids_character', wp_json_encode( $character ) );
			// JSON encode for structured data storage.
		}

		return $user_id;
	}

	/**
	 * Authenticates user login by email.
	 *
	 * @param string $email The email of the user.
	 * @return void
	 */
	public static function authenticate_user( $email ) {
		$email = sanitize_email( $email );
		$user  = get_user_by( 'email', $email );

		if ( $user ) {
			wp_set_auth_cookie( $user->ID, true );
			wp_safe_redirect( home_url() );
			exit();
		}

		set_transient( 'coolkids_login_failed', __( 'Oops! Email not registered. Please try again.', 'ckn' ), 30 );
		wp_safe_redirect( home_url( '/login/' ) );
		exit();
	}

	/**
	 * Retrieves the avatar image URL for each role.
	 *
	 * @param string $role The role of the user.
	 * @return string Image URL.
	 */
	public static function get_role_image( $role ) {
		$role_images = array(
			'cool_kid'    => 'cool-kid.webp',
			'cooler_kid'  => 'cooler-kid.webp',
			'coolest_kid' => 'coolest-kid.webp',
		);

		return CKN_URL . 'assets/public/img/' . ( $role_images[ $role ] ?? 'cool-kid.webp' );
	}

	/**
	 * Retrieves the role class name.
	 *
	 * @param string $role The user role.
	 * @return string Role class.
	 */
	public static function get_role_class( $role ) {
		$role_classes = array(
			'cool_kid'    => 'cool-kid',
			'cooler_kid'  => 'cooler-kid',
			'coolest_kid' => 'coolest-kid',
		);
		$role_class   = $role_classes[ $role ] ?? 'cool-kid';
		return $role_class;
	}

	/**
	 * Retrieves user data for the logged-in user.
	 *
	 * @return array|null User data or null if not logged in.
	 */
	public static function get_user_data() {
		if ( ! is_user_logged_in() ) {
			return null;
		}

		$user = wp_get_current_user();
		$role = $user->roles[0] ?? 'cool_kid';

		$character = get_user_meta( $user->ID, 'cool_kids_character', true );

		return array(
			'user'       => $user,
			'role'       => $role,
			'role_class' => self::get_role_class( $role ),
			'image_url'  => self::get_role_image( $role ),
			'character'  => array(
				'first_name' => $character['first_name'] ?? '',
				'last_name'  => $character['last_name'] ?? '',
				'country'    => $character['country'] ?? '',
				'email'      => $user->user_email,
			),
		);
	}

	/**
	 * Generates the HTML structure to display other user profile.
	 *
	 * @param WP_User     $user_obj The user object.
	 * @param string|null $current_user_role The role of the currently logged-in user.
	 *
	 * @return string The generated user card HTML.
	 */
	public static function generate_user_card_html( $user_obj, $current_user_role = null ) {
		$user_role    = $user_obj->roles[0] ?? 'cool_kid';
		$user_country = get_user_meta( $user_obj->ID, 'country', true ) ? get_user_meta( $user_obj->ID, 'country', true ) : esc_html__( 'Unknown', 'ckn' );
		$image_url    = esc_url( self::get_role_image( $user_role ) );
		$role_class   = esc_attr( self::get_role_class( $user_role ) );
		$user_name    = esc_html( "{$user_obj->first_name} {$user_obj->last_name}" );
		$user_email   = esc_html( $user_obj->user_email );

		// Role block: Only visible to 'coolest_kid'.
		$role_block = ( 'coolest_kid' === $current_user_role )
			? sprintf(
				'<p class="role is-relative text-align-center text-is-bold width-is-fit-content m-0-auto has-rounded-radius %s">%s!</p>',
				esc_attr( $role_class ),
				esc_html( ucwords( str_replace( '_', ' ', $user_role ) ) )
			)
			: '';

		$email_block = ( 'coolest_kid' === $current_user_role )
			? sprintf( '<p class="email">%s</p>', esc_html( $user_email ) )
			: '';

		return sprintf(
			'<div class="user-card with-padding is-grid has-global-gap has-square-slight-rounded-radius text-align-center">
	            <figure class="avatar m-0-auto">
	                <img loading="lazy" src="%s" width="110" height="110" alt="%s" class="has-circle-radius">
	                %s
	            </figure>
	            <div class="user-info is-grid is-justify-center">
	                <p class="name text-is-bold">%s</p>
	                <p class="country">%s</p>
	                %s
	            </div>
	        </div>',
			$image_url,
			esc_attr__( 'User Image', 'ckn' ),
			$role_block, // Role only shown to 'coolest_kid'.
			$user_name,
			$user_country,
			$email_block // Email only shown to 'coolest_kid'.
		);
	}

	/**
	 * Logs error messages to the WordPress debug log if WP_DEBUG_LOG is enabled.
	 *
	 * This function ensures that errors are logged only when debugging is active,
	 * preventing unnecessary logging in production environments.
	 *
	 * @param string $message The error message to log.
	 */
	public static function log_error( $message ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( $message ); // phpcs:ignore
		}
	}
}