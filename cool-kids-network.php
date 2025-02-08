<?php

/**
* Plugin Name: Cool Kids Network
* Description: WordPress developer Technical Assessment
* Author: Arya Dhiratara
* Author URI: https://dhiratara.com/
* Version: 0.0.1
* Requires at least: 5.8
* Requires PHP: 7.4
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: ckn
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define( 'CKN_VER', '0.0.1' );
define( 'CKN_SLUG', 'cool-kids-network' );
define( 'CKN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CKN_URL', plugin_dir_url( __FILE__ ) );

require_once dirname(__FILE__) . '/vendor/autoload.php';
