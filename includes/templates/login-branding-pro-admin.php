<div class="hashtags-page">
  <h1><?php _e('Custom Login Page', 'custom-login-page') ?></h1>
  <div class="container">
    <div class="column about">
      <h3 class="title"><?php _e('About', 'custom-login-page') ?></h3>
      <p>
        <?php _e('Customizes the WordPress login page with your branding.', 'custom-login-page') ?>
      </p>
    </div>
    <div class="column settings">
      <h3 class="title"><?php _e('Settings', 'custom-login-page') ?></h3>
      <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
          <?php $nonce = wp_create_nonce('update_login_page_settings'); ?>
          <div class="form-row">
            <label for="color"><?php _e('Choose the background color of the page', 'custom-login-page') ?></label>
            <input type="color" name="color" value="<?php echo esc_attr($this->color); ?>">
          </div>
          <div class="form-row">
            <?php if (!$this->logo) : ?>
              <label for="logo"><?php _e('Upload your logo', 'custom-login-page') ?></label>
              <input type="file" name="logo" accept="image/*">
            <?php endif; ?>
            <?php if ($this->logo) : ?>
              <label for="logo" id="logo-uploaded">
                <span><?php _e('Change your logo', 'custom-login-page') ?></span>
                <div class="logo-preview">
                  <img src="<?php echo esc_url($this->logo); ?>" alt="Logo Preview">
                </div>
              </label>
              <input type="file" name="logo" accept="image/*">
            <?php endif; ?>
          </div>
          <div class="form-row">
            <label for="link"><?php _e('Change the link of the logo', 'custom-login-page') ?></label>
            <input type="text" name="link" value="<?php echo esc_attr($this->link); ?>">
          </div>
          <input type="hidden" name="action" value="update_login_page_settings">
          <?php wp_nonce_field('update_login_page_settings', 'update_login_page_settings_nonce'); ?>
          <input type="submit" value="Save">
          <button type="button" id="reset-form"><?php _e('Reset Form', 'custom-login-page') ?></button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function($) {
    $('#reset-form').on('click', function() {
      // Reset to default options
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'reset_login_page_settings'
        },
        success: function(response) {
          // Handle success response
          location.reload();
        },
        error: function(xhr, status, error) {
          // Handle error response
        }
      });
    });
  });
</script>