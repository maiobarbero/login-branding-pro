<?php

namespace MWDCustomLoginPage;

class CustomLoginPage
{
  const DOMAIN = 'custom-login-page';
  public $color;
  public $logo;
  public $link;
  public function __construct()
  {

    $this->color = get_option('custom_login_page_color', get_background_color());
    $this->logo = get_option('custom_login_page_logo', '/wp-admin/images/wordpress-logo.svg?ver=20131107');
    $this->link = get_option('custom_login_page_link', 'https://wordpress.org/');

    add_action('init', [$this, '_clp_init']);
  }

  public function _clp_init()
  {
    add_action('admin_enqueue_scripts', [$this, '_clp_enqueue_styles']);
    add_action('admin_menu', [$this, '_clp_admin_menu_page']);
    add_action('login_head', [$this, '_clp_change_logo_and_bg']);
    add_action('wp_ajax_reset_login_page_settings', [$this, '_clp_function_reset_callback']);

    add_filter('login_headerurl', [$this, '_clp_logo_url']);
  }
  public function _clp_admin_menu_page()
  {
    add_submenu_page(
      'options-general.php', // Parent slug
      'custom-login-page', // Page title
      __('Login Page', self::DOMAIN), // Menu title
      'manage_options', // Capability
      'custom-login-page', // Menu slug
      [$this, '_clp_update_options'] // Callback function to display the page
    );
  }
  public function _clp_enqueue_styles()
  {
    wp_register_style('custom-login-page-style', plugin_dir_url(__FILE__) . 'assets/custom-login-page-style.css');
    wp_enqueue_style('custom-login-page-style');
  }
  public function _clp_change_logo_and_bg()
  {
    echo "<style type=\"text/css\">
        body{background-color:$this->color !important;}
        .login h1 a { background-image: url($this->logo) !important; width: 230px !important; height: 200px !important;}
        .login h1 a { background-size: 230px 200px !important;}
        #login {padding: 54px 0 0 !important;}
        </style>";
  }
  public function _clp_logo_url()
  {
    return $this->link;
  }
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
  }

  public function _clp_function_reset_callback()
  {
    update_option('custom_login_page_color', get_background_color());
    update_option('custom_login_page_logo', '/wp-admin/images/wordpress-logo.svg?ver=20131107');
    update_option('custom_login_page_link', 'https://wordpress.org/');

    wp_send_json_success();
  }
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
