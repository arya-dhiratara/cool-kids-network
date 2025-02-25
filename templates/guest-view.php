<?php
/**
 * Guest View Template for Cool Kids Network
 *
 * This template displays the welcome section for guest users.
 * It includes a sign-up and login button for user registration.
 *
 * @package    CoolKidsNetwork
 */

?>
<div class="coolkids-home top m-0-auto is-grid has-2-column-grid-desktop has-very-large-global-gap has-rm-bg-colors with-padding has-square-rounded-radius">
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
		<p><?php esc_html_e( 'Join the coolest network of kids from around the world by signing up now!', 'ckn' ); ?></p>
		<div class="buttons is-flex is-flex-wrap has-very-small-global-gap has-small-margin-top m-t-auto-desktop">
			<a href="<?php echo esc_url( home_url( '/signup/' ) ); ?>" class="button ckn-button ckn-button-padding signup has-rounded-radius with-bg-color">
				<?php esc_html_e( 'Register', 'ckn' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/login/' ) ); ?>" class="button ckn-button ckn-button-padding login">
				<?php esc_html_e( 'Log In', 'ckn' ); ?>
			</a>
		</div>
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
