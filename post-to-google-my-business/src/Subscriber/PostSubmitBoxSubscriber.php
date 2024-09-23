<?php

namespace PGMB\Subscriber;

use PGMB\EventManagement\SubscriberInterface;
use PGMB\Premium\PostTypes\PostCampaign;
class PostSubmitBoxSubscriber implements SubscriberInterface {
    private $enabled_post_types;

    protected $invert_checkbox;

    public function __construct( $enabled_post_types, $invert_checkbox ) {
        $this->enabled_post_types = $enabled_post_types;
        $this->invert_checkbox = $invert_checkbox;
    }

    public static function get_subscribed_hooks() {
        return [
            'post_submitbox_misc_actions' => 'render_auto_post_checkbox',
            'save_post'                   => 'save_autopost_checkbox_value',
        ];
    }

    /**
     * Check if the post was created from the editor or through an external source
     *
     * mbp_wp_post isn't set when the post is created outside of the Classic Editor
     *
     * @return bool Post was created through classic editor
     */
    public function is_wp_post_submission() {
        if ( isset( $_POST['mbp_wp_post'] ) ) {
            return true;
        }
        return false;
    }

    /**
     * Check if the post was submitted through the editor and save the autopost checkbox value
     *
     * @param $post_id
     * @since 2.2.11
     */
    public function save_autopost_checkbox_value( $post_id ) {
        $submitted = $this->is_wp_post_submission();
        if ( !$submitted ) {
            return;
        }
        //		$gutenberg_checkbox = get_post_meta($post_id, "_mbp_gutenberg_autopost", true);
        $checked = isset( $_POST['mbp_create_post'] ) && $_POST['mbp_create_post'];
        update_post_meta( $post_id, 'mbp_autopost_checked', $checked );
    }

    /**
     * Check whether the auto-post checkbox was checked
     *
     * @param $post_id
     *
     * @return bool Checkbox checked
     */
    public function is_autopost_checkbox_checked( $post_id ) {
        if ( get_post_meta( $post_id, 'mbp_autopost_checked', true ) ) {
            return true;
        }
        return false;
    }

    /**
     * Return whether the Autopost checkbox has to be checked on the form
     *
     * @return mixed HTML content
     */
    public function get_autopost_checkbox_checked() {
        $current_screen = get_current_screen();
        $isNewPost = $current_screen->action === 'add';
        if ( $isNewPost ) {
            //			return $this->settings_api->get_option('invert', 'mbp_quick_post_settings', 'off') == 'on';
            return $this->invert_checkbox;
        }
        $hasAutoPosted = get_post_meta( get_the_ID(), 'mbp_autopost_created', true );
        $isScheduled = get_post_meta( get_the_ID(), '_pgmb_scheduled_autopost', true );
        $isCheckboxChecked = $this->is_autopost_checkbox_checked( get_the_ID() );
        return $isCheckboxChecked && !$hasAutoPosted;
    }

    /**
     * Render the auto-post checkbox in the post publish section
     *
     * @return void
     */
    public function render_auto_post_checkbox() {
        if ( !in_array( get_post_type(), $this->enabled_post_types ) ) {
            return;
        }
        ?>
		<div class="misc-pub-section misc-pub-section-last mbp-autopost-checkbox-container">
			<label><input type="checkbox" id="mbp_create_post" value="1" name="mbp_create_post" <?php 
        checked( $this->get_autopost_checkbox_checked() );
        ?>/>
				<?php 
        _e( 'Auto-post to GMB on Publish/Update', 'post-to-google-my-business' );
        ?>
			</label>
		</div>
		<?php 
    }

}
