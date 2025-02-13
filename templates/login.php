<?php
/**
 * Login Template for Cool Kids Network
 *
 * This template displays the login page.
 * It includes a login button for user login and additional sign-up button.
 *
 * The transient `coolkids_login_failed` is used to store temporary error messages
 * when a login attempt fails. Once displayed, it is deleted to prevent persistent errors.
 *
 * @package    CoolKidsNetwork
 */

use CoolKidsNetwork\Utils;

$login_failed = get_transient( 'coolkids_login_failed' );

// Delete the message after displaying it.
if ( $login_failed ) {
	delete_transient( 'coolkids_login_failed' );
}
?>

<div class="coolkids-login top m-0-auto is-grid has-2-column-grid-desktop has-very-large-global-gap has-rm-bg-colors with-padding has-square-rounded-radius">
	<div class="left is-flex is-flex-column has-small-global-gap order-2-on-mobile with-small-padding-on-desktop">
		<h2 class="text-is-bold">
			<?php
				printf(
					/* translators: %s is site title */
					esc_html__( 'Welcome to the %s', 'ckn' ),
					'<span class="larger is-flex">' . esc_html__( 'Cool Kids Network', 'ckn' ) . '!</span>'
				);
				?>
		</h2>

		<?php if ( $login_failed ) : ?>
			<p class="failed-message width-is-fit-content has-rounded-radius">
				<?php echo esc_html( $login_failed ); ?>
			</p>
		<?php else : ?>
			<p><?php esc_html_e( 'Have an account? Log in using your email.', 'ckn' ); ?></p>
		<?php endif; ?>

		<?php echo wp_kses_post( Utils::generate_login_form_html() ); ?>
	</div>

	<div class="right m-l-auto with-welcome-img is-flex is-flex-column is-justify-center">
		<figure class="welcome">
			<img 
				loading="eager" 
				fetchpriority="high" 
				src="<?php echo esc_url( CKN_URL . 'assets/public/img/cool-kids-network.webp' ); ?>" 
				width="300" 
				height="300" 
				alt="<?php echo esc_attr__( 'Welcome Image', 'ckn' ); ?>"
			>
		</figure>
	</div>
</div>

