<?php

namespace MWDLoginBrandingPro;

/**
 * Summary of LoginBrandingPro
 * @author Matteo Barbero
 * @copyright (c) 2023
 */
class LoginBrandingPro
{
  const DOMAIN = 'custom-login-page';
  public $color;
  public $logo;
  public $link;
  public $plugin_version;
  /**
   * Summary of __construct
   * @param string $plugin_version
   */
  public function __construct($plugin_version)
  { 


    $this->color = get_option('custom_login_page_color', get_background_color());
    $this->logo = get_option('custom_login_page_logo', '/wp-admin/images/wordpress-logo.svg?ver=20131107');
    $this->link = get_option('custom_login_page_link', 'https://wordpress.org/');

    $this->plugin_version = $plugin_version;

    add_action('init', [$this, '_clp_init']);
  }

  /**
   * Summary of _clp_init
   * @return void
   */
  public function _clp_init()
  {
    add_action('admin_enqueue_scripts', [$this, '_clp_enqueue_styles']);
    add_action('admin_menu', [$this, '_clp_admin_menu_page']);
    add_action('login_head', [$this, '_clp_change_logo_and_bg']);
    add_action('wp_ajax_reset_login_page_settings', [$this, '_clp_function_reset_callback']);

    add_filter('login_headerurl', [$this, '_clp_logo_url']);
  }
  /**
   * Summary of _clp_admin_menu_page
   * @return void
   */
  public function _clp_admin_menu_page()
  {
    add_submenu_page(
      'options-general.php', // Parent slug
      'login-branding-pro-page', // Page title
      __('Login Branding Pro', self::DOMAIN), // Menu title
      'manage_options', // Capability
      'login-branding-pro-page', // Menu slug
      [$this, '_clp_update_options'] // Callback function to display the page
    );
    
  }
  /**
   * Summary of _clp_enqueue_styles
   * @return void
   */
  public function _clp_enqueue_styles()
  {
    wp_register_style('custom-login-page-style', plugin_dir_url(__FILE__) . '/assets/login-branding-pro-style.css',[],$this->plugin_version,'all');
    wp_enqueue_style('custom-login-page-style');
  }
  /**
   * Summary of _clp_change_logo_and_bg
   * @return void
   */
  public function _clp_change_logo_and_bg()
  {
    echo "<style type=\"text/css\">
        body{background-color:$this->color !important;}
        .login h1 a { background-image: url($this->logo) !important; width: 230px !important; height: 200px !important;}
        .login h1 a { background-size: 230px 200px !important;}
        #login {padding: 54px 0 0 !important;}
        </style>";
  }
  /**
   * Summary of _clp_logo_url
   * @return string
   */
  public function _clp_logo_url()
  {
    return $this->link;
  }
  /**
   * Summary of _clp_update_options
   * @return void
   */
  public function _clp_update_options()
  {
    if (isset($_POST['action']) && $_POST['action'] == 'update_login_page_settings') {
      if (isset($_POST['update_login_page_settings_nonce']) && wp_verify_nonce($_POST['update_login_page_settings_nonce'], 'update_login_page_settings')) {

        $this->color = sanitize_hex_color($_POST['color']);
        $this->link = sanitize_url($_POST['link']);

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
          $logo_file = $this->_clp_sanitize_image($_FILES['logo']);

          // Handle the file upload and retrieve the attachment ID
          $attachment_id = media_handle_upload('logo', 0);

          if (!is_wp_error($attachment_id)) {
            // Get the attachment URL and set it as the logo
            $this->logo = wp_get_attachment_url($attachment_id);
          }
        }
        // Save the updated options
        update_option('custom_login_page_color', $this->color);
        update_option('custom_login_page_link', $this->link);
        update_option('custom_login_page_logo', $this->logo);

        echo "<script>window.location.reload();</script>";
      }
    }
    require_once plugin_dir_path(__FILE__) . '/templates/login-branding-pro-admin.php';
  }
   /**
    * Summary of _clp_function_reset_callback
    * @return void
    */
  public function _clp_function_reset_callback()
  {
    update_option('custom_login_page_color', get_background_color());
    update_option('custom_login_page_logo', '/wp-admin/images/wordpress-logo.svg?ver=20131107');
    update_option('custom_login_page_link', 'https://wordpress.org/');

    wp_send_json_success();
  }
  /**
   * Summary of _clp_sanitize_image
   * @param mixed $input
   * @return mixed
   */
  protected function _clp_sanitize_image($input)
  {

    /* default output */
    $output = '';

    /* check file type */
    $filetype = wp_check_filetype($input);
    $mime_type = $filetype['type'];

    /* only mime type "image" allowed */
    if (strpos($mime_type, 'image') !== false) {
      $output = $input;
    }

    return $output;
  }
}
