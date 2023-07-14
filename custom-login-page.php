<?php

/**
 * Plugin Name: Login Branding Pro
 * Plugin URI: https://example.com/
 * Description: Customizes the WordPress login page with your branding.
 * Version: 1.0.0
 * Author: Matteo Barbero
 * Author URI: https://profiles.wordpress.org/maiobarbero/
 */

use MWDCustomLoginPage\CustomLoginPage;

if (!defined('ABSPATH')) exit;

require_once(plugin_dir_path(__FILE__) . 'includes/CustomLoginPage.php');

function custom_login_page_init()
{
  $custom_login_page = new CustomLoginPage();
}
add_action('plugins_loaded', 'custom_login_page_init');