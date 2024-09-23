<?php use PGMB\Admin\AdminPage;

if( $this instanceof AdminPage) : ?>
<div class="wrap mbp-settings">
    <h2><?php esc_html_e('Post to Google My Business settings', 'post-to-google-my-business'); ?></h2>
	<?php settings_errors(); ?>
	<p><?php esc_html_e('Thanks for choosing Post to Google My Business! The easiest and most versatile Google My Business plugin for WordPress.', 'post-to-google-my-business'); ?></p>
	<p><?php _e('Need help getting started? Check out the <a target="_blank" href="https://tycoonmedia.net/gmb-tutorial-video/">tutorial video</a>', 'post-to-google-my-business'); ?></p>
	<?php $this->settings_api->show_navigation(); ?>
	<?php $this->settings_api->show_forms(); ?>
</div>



<div id="multi-account-upgrade-notification" style="display:none;">
    <div class="mbp-thickbox-inner">
        <img src="<?php echo $this->plugin_url . 'img/plugin-icon.png'; ?>" style="float:right; width:128px; padding-left:10px;" alt="Plugin icon" />
        <p>
            <?php _e('Upgrade to the <strong>Agency</strong> plan of <i>Post to Google My Business</i> to handle multiple Google accounts within a single installation of the plugin. With Post to Google My Business for Agencies, you can:', 'post-to-google-my-business'); ?>
        </p>
        <ul>
            <li><?php _e('Connect <strong>multiple</strong> Google accounts to a single website', 'post-to-google-my-business'); ?></li>
            <li><?php _e('Assign accounts & GMB locations to specific WordPress users (and hide others)', 'post-to-google-my-business'); ?></li>
            <li><?php _e('Let your users add &amp; manage their own Google accounts', 'post-to-google-my-business'); ?></li>
            <li><?php _e('Publish posts to multiple locations across multiple Google accounts at once', 'post-to-google-my-business'); ?></li>
        </ul>
        <br />
        <a class="button button-primary" href="<?php echo mbp_fs()->get_upgrade_url(); ?>"><?php _e('View pricing &amp; buy now &raquo;', 'post-to-google-my-business'); ?></a>
    </div>
</div>
<?php endif; ?>
