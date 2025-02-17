<?php
/**
 * Enqueue Class
 *
 * Handles the enqueuing of styles and scripts for the Cool Kids Network.
 *
 * @package CoolKidsNetwork
 */

namespace CoolKidsNetwork;

defined( 'ABSPATH' ) || exit;

/**
 * Class Enqueue
 *
 * Manages the loading of styles and scripts.
 */
class Enqueue {
	/**
	 * Initialize script and style loading.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_styles' ), 99 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ), 99 );
	}

	/**
	 * Load stylesheets and inline styles.
	 *
	 * @return void
	 */
	public static function load_styles() {
		global $post;

		wp_register_style(
			CKN_SLUG . '-style',
			CKN_URL . 'assets/public/css/styles.css',
			array(),
			// Version with daily cache bust for dev.
			CKN_VER . '-' . gmdate( 'Ymd' )
		);

		// Load styles only if the shortcode presence.
		$has_home_shortcode   = is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'coolkids_home' );
		$has_login_shortcode  = is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'coolkids_login' );
		$has_signup_shortcode = is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'coolkids_signup' );

		if ( $has_home_shortcode || $has_login_shortcode || $has_signup_shortcode ) {
			wp_enqueue_style( CKN_SLUG . '-style' );
		}

		$inline_styles = '';

		if ( $has_login_shortcode || $has_signup_shortcode ) {
			$inline_styles .= "
				.ckn-button,
				.coolkids-form input[type='email' i] {
					font-size: 92%;
					font-family: inherit;
					border: 0;
				}
				.coolkids-form input[type='email' i] {
					background-color: rgba(255, 255, 255, .88);
					padding: 0 12px;
					min-height: 40px;
				}
				.coolkids-form button {
					min-height: 42px;
				}
				.coolkids-form {
					max-width: calc(210px + 7.5vw);
				}
				.coolkids-form.is-flex-column button {
					margin-top: 2.5px;
				}
				.failed-message {
					font-size: 93%;
					padding: 1.5px 14px;
					box-shadow: inset 0 0 80px 0 rgba(0, 0, 0, .7);
				}
			";
		}

		if ( $has_home_shortcode ) {
			$inline_styles .= "
				.coolkids-user-list {
					grid-template-columns: repeat(auto-fit, minmax(clamp(160px, 20vw, 180px), 1fr));
				}
				.coolkids-profile figure.avatar {
					width: 200px;
					max-width: 33.85vw;
				}
				.coolkids-user-list figure.avatar {
					width: 110px;
					max-width: 33.85vw;
				}
				.cool-kid { background-color: #4CAF50; } /* Green */
				.cooler-kid { background-color: #2196F3; } /* Blue */
				.coolest-kid { background-color: #FF9800; } /* Orange */
				div[class^='coolkids'] .role {
					padding: 4.4px clamp(16px,calc(16px + (25 - 16) * ((100vw - 360px) / (1920 - 360))),25px);
					margin-top: -.9em;
					font-size: 88%;
					color: white;
					z-index: 1;
				}
				.user-card {
					border: 2px solid #e84b85;
					border-style: outset groove;
					font-size: 78%;
					padding: calc(1.35em + .15vw);
				}
				.user-card p {
					overflow: scroll;
					white-space: nowrap;
					max-width: 156px;
					text-overflow: clip;
				}
				.user-card .name {
					font-size: 110%;
				}
				.user-card .email {
					font-size: 96%;
				}
				.coolkids-load-more .ckn-button {
					border: 1px solid;
				}
				@media (max-width: 767.9px) {
					h2 span:not(.user-name) {
						display: flex;
					}
					h2 .user-name {
						font-size: 128%;
					}
				}
			";
		}

		// Inject inline styles if not empty.
		if ( ! empty( $inline_styles ) ) {
			wp_add_inline_style( CKN_SLUG . '-style', $inline_styles );
		}
	}

	/**
	 * Load scripts with AJAX support.
	 *
	 * @return void
	 */
	public static function load_scripts() {
		global $post;

		$has_home_shortcode = is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'coolkids_home' );

		wp_register_script(
			CKN_SLUG . '-ajax-users',
			CKN_URL . 'assets/public/js/load-more-users.js',
			array(),
			// Version with daily cache bust for dev.
			CKN_VER . '-' . gmdate( 'Ymd' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		if ( $has_home_shortcode ) {
			wp_enqueue_script( CKN_SLUG . '-ajax-users' );
			wp_localize_script(
				CKN_SLUG . '-ajax-users',
				'coolKidsAjax',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'load_more_users' ),
				)
			);
		}
	}
}
