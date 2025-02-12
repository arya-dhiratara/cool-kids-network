<?php
/**
 * SignUp Template for Cool Kids Network
 *
 * This template displays the user registration page.
 * It includes a sign-up button for user registration and additional login button.
 *
 * The transients `coolkids_signup_success` and `coolkids_signup_failed`
 * are used to temporarily store signup messages. They are deleted after being displayed
 * to ensure messages do not persist across page reloads.
 *
 * @package    CoolKidsNetwork
 */

use CoolKidsNetwork\Utils;

$signup_success = get_transient( 'coolkids_signup_success' );
$signup_failed  = get_transient( 'coolkids_signup_failed' );

// Delete messages after displaying them.
if ( $signup_success ) {
	delete_transient( 'coolkids_signup_success' );
}
if ( $signup_failed ) {
	delete_transient( 'coolkids_signup_failed' );
}
?>

<div class="coolkids-login top m-0-auto is-grid has-2-column-grid-desktop has-very-large-global-gap has-rm-bg-colors with-padding has-square-rounded-radius">
	<div class="left is-flex is-flex-column has-small-global-gap order-2-on-mobile with-small-padding-on-desktop">
		<h2 class="text-is-bold">Welcome to the <span class="larger is-flex">Cool Kids Network!</span></h2>

		<?php if ( $signup_success ) : ?>
			<p class="success-message">Yay! You're officially signed up! <a href="<?php echo esc_url( home_url( '/login/' ) ); ?>">Log in here</a> to meet other cool kids!</p>
		<?php elseif ( $signup_failed ) : ?>
			<p class="failed-message width-is-fit-content has-rounded-radius"><?php echo esc_html( $signup_failed ); ?></p>
			<?php echo esc_html( Utils::generate_signup_form_html() ); ?>
		<?php else : ?>
			<p>Create an account using your email.</p>
			<?php echo esc_html( Utils::generate_signup_form_html() ); ?>
		<?php endif; ?>
	</div>
	<div class="right m-l-auto with-welcome-img is-flex is-flex-column is-justify-center">
		<figure class="welcome">
			<img loading="eager" fetchpriority="high" src="<?php echo esc_url( CKN_URL . 'assets/public/img/cool-kids-network.webp' ); ?>" width="300" height="300" alt="Welcome Image">
		</figure>
	</div>
</div>