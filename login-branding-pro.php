<?php

/**
 * Plugin Name: Login Branding Pro
 * Plugin URI: https://example.com/
 * Description: Customizes the WordPress login page with your branding.
 * Version: 1.0.0
 * Author: Matteo Barbero
 * Author URI: https://profiles.wordpress.org/maiobarbero/
 */

use MWDLoginBrandingPro\LoginBrandingPro;

if (!defined('ABSPATH')) exit;

require_once(plugin_dir_path(__FILE__) . 'includes/LoginBrandingPro.php');

/**
 * Summary of login_branding_pro_page_init
 * @return void
 */
function login_branding_pro_page_init()
{
  $plugin_data = get_file_data(__FILE__, [
    'version' => 'Version',
  ],
    'plugin'
  );
 
  $custom_login_page = new LoginBrandingPro($plugin_data['version']);
}
add_action('plugins_loaded', 'login_branding_pro_page_init');