<?php
/**
 * User View Template for Cool Kids Network
 *
 * This template displays the profile section for logged-in users.
 * It includes the current user profile as well as other user profiles,
 * depending on the role.
 *
 * @package    CoolKidsNetwork
 */

use CoolKidsNetwork\Utils;

$user_data = Utils::get_user_data();

if ( ! $user_data ) {
	wp_safe_redirect( wp_login_url() );
	exit();
}

$character        = $user_data['character'];
$ckn_role         = $user_data['role'];
$ckn_role_class   = $user_data['role_class'];
$image_url        = $user_data['image_url'];
$ckn_current_user = $user_data['user'];

$can_view_users = in_array( $ckn_role, array( 'cooler_kid', 'coolest_kid' ), true );

// Fetch users (excluding current user).
$args = array(
	'number'   => 13,
	'orderby'  => 'registered',
	'order'    => 'DESC',
	'role__in' => array( 'cool_kid', 'cooler_kid', 'coolest_kid' ),
	'exclude'  => array( $ckn_current_user->ID ),
);

$users = get_users( $args );

$ckn_users = wp_get_current_user();
$user_role = $ckn_users->roles[0] ?? '';

// Check if there are more than 12 users.
$has_more_users = count( $users ) > 12;
$users          = array_slice( $users, 0, 12 );

?>

<!-- Current User Profile -->
<div class="coolkids-profile top m-0-auto is-grid has-very-large-global-gap has-rm-bg-colors with-padding has-square-rounded-radius text-has-subpixel-antialiased">
	<!-- top -->
	<div class="top is-grid has-2-column-grid has-very-large-global-gap">
		<div class="left">
			<figure class="avatar">
				<img src="<?php echo esc_url( $image_url ); ?>" width="128" height="128" alt="<?php esc_attr_e( 'Profile Image', 'ckn' ); ?>" class="has-circle-radius">
				<p class="role is-relative text-align-center text-is-bold width-is-fit-content m-0-auto has-rounded-radius <?php echo esc_attr( $ckn_role_class ); ?>">
					<?php echo esc_html( ucwords( str_replace( '_', ' ', $ckn_role ) ) ); ?> !
				</p>
			</figure>
		</div>
		<div class="right is-flex is-flex-column has-small-global-gap m-l-auto">
			<h2><span><?php esc_html_e( 'Hey', 'ckn' ); ?></span> <span class="text-is-bold user-name"><?php echo esc_html( $character['first_name'] ?? esc_html__( 'Cool Kid', 'ckn' ) ); ?></span>, <span><?php esc_html_e( 'Welcome Back!', 'ckn' ); ?></span></h2>
			<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="ckn-button width-is-fit-content"><?php esc_html_e( 'Logout', 'ckn' ); ?></a>
		</div>
	</div>
	<!-- bottom -->
	<ul class="is-grid has-very-small-global-gap">
		<li><strong><?php esc_html_e( 'Name:', 'ckn' ); ?></strong> <?php echo esc_html( $character['first_name'] . ' ' . $character['last_name'] ); ?></li>
		<li><strong><?php esc_html_e( 'Country:', 'ckn' ); ?></strong> <?php echo esc_html( $character['country'] ); ?></li>
		<li><strong><?php esc_html_e( 'Email:', 'ckn' ); ?></strong> <?php echo esc_html( $ckn_current_user->user_email ); ?></li>
		<li><strong><?php esc_html_e( 'Role:', 'ckn' ); ?></strong> <?php echo esc_html( ucwords( str_replace( '_', ' ', $ckn_role ) ) ); ?></li>
	</ul>
</div>

<!-- Other User Profile List -->
<?php if ( $can_view_users ) : ?>
	<div id="coolkids-user-list" class="coolkids-user-list m-0-auto is-grid has-global-gap has-large-margin-top">
		<h2 class="text-is-bold grid-span-full"><?php esc_html_e( 'Meet other Cool Kids!', 'ckn' ); ?></h2>
		<?php foreach ( $users as $user_obj ) : ?>
			<?php echo wp_kses_post( Utils::generate_user_card_html( $user_obj, esc_attr( $user_role ) ) ); ?>
		<?php endforeach; ?>
	</div>

	<?php if ( $has_more_users ) : ?>
		<div class="coolkids-load-more m-0-auto is-grid has-very-small-global-gap is-justify-center has-margin-top">
			<button id="load-more-users" data-offset="12" class="ckn-button ckn-button-padding has-rounded-radius with-bg-img-on-hover"><?php esc_html_e( 'Load More', 'ckn' ); ?></button>
		</div>
	<?php endif; ?>
<?php endif; ?>
