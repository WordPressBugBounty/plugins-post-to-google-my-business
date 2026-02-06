<?php
    $anchor_text  = esc_html_x('the auto-post settings', 'Link to settings page', 'post-to-google-my-business');
    $settings_url = esc_url(admin_url('admin.php?page=pgmb_settings#mbp_quick_post_settings'));
    $link         = sprintf( '<a href="%s">%s</a>', $settings_url, $anchor_text );
?>
<div class="wrap mbp-settings">
    <h2><?php esc_html_e('Multiple auto-post templates are a Post to Google My Business "Professional" feature', 'post-to-google-my-business'); ?></h2>

    <p>
        <?php
            /* translators: %s is link to the auto-post template settings page "the auto-post settings" */
            printf(esc_html__('You can modify the default auto-post template in %s.', 'post-to-google-my-business'), $link );
        ?>
    </p>

    <img src="<?php echo $this->plugin_url; ?>/img/promotional/autopost-templates.png" style="float:right;" alt="<?php esc_attr_e('Post campaigns screenshot', 'post-to-google-my-business'); ?>" />

    <p><strong><?php esc_html_e('Post to Google my Business Professional lets you:', 'post-to-google-my-business'); ?></strong></p>
    <ul>
        <li><?php esc_html_e('Create multiple auto-post templates for different types of content', 'post-to-google-my-business'); ?></li>
        <li><?php esc_html_e('Assign auto-post templates to specific post types', 'post-to-google-my-business'); ?></li>
        <li><?php esc_html_e('Pick an auto-post template before publishing', 'post-to-google-my-business'); ?></li>
        <li><?php esc_html_e('Publish posts to multiple locations at once', 'post-to-google-my-business'); ?></li>
    </ul>
    <br />
    <a class="button button-primary" href="<?php echo mbp_fs()->get_upgrade_url(); ?>"><?php esc_html_e('View pricing &amp; buy now &raquo;', 'post-to-google-my-business'); ?></a>
</div>
