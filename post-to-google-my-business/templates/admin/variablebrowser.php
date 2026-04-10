<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="pgmb-variable-panel">
	<div class="pgmb-panel-content">
        <h2><?php esc_html_e('Variables/preview', 'post-to-google-my-business'); ?></h2>
        <div id="pgmb-post-browser">
            <label>
                <?php esc_html_e('Post type', 'post-to-google-my-business'); ?>
                <select id="pgmb-vp-post-type">
                    <option disabled="disabled"><?php esc_html_e('Post type', 'post-to-google-my-business'); ?></option>
                    <?php
                        foreach($this->enabled_post_types as $post_type){
                            if($post_type === 'mbp-google-posts') continue;
                            echo '<option value="'.$post_type.'">'.$post_type.'</option>';
                        }
                    ?>
                </select>
            </label>
            <div id="pgmb-selected-post"></div>
            <div id="pgmb-selected-post-controls">
                <button class="button button-secondary button-small button-previous"><?php esc_html_e('Previous', 'post-to-google-my-business');?></button>
                <button class="button button-secondary button-small button-next" disabled="disabled"><?php esc_html_e('Next', 'post-to-google-my-business');?></button>
            </div>
        </div>
        <hr style="clear:both;"/>

        <div class="nav-tab-wrapper">
            <a href="#pgmb-variables" class="nav-tab nav-tab-active"><?php esc_html_e('Dynamic data', 'post-to-google-my-business'); ?></a>
            <a href="#pgmb-preview" class="nav-tab"><?php esc_html_e('Post preview', 'post-to-google-my-business'); ?></a>
        </div>

        <div id="pgmb-variables" class="pgmb-var-tab">

            <h3><?php esc_html_x('Dynamic data', '"Variables" refers to the placeholder tokens in the post text', 'post-to-google-my-business');?></h3>

            <ul id="pgmb-variable-tree-container"></ul>
            <hr />

            <div id="pgmb-variable-details"></div>

        </div>


        <div id="pgmb-preview" class="pgmb-var-tab">
            <h3><?php esc_html_e('Post Preview', 'post-to-google-my-business');?></h3>
            <div class="pgmb-post-preview-container">

            </div>
            <br />
            <button class="button button-primary" id="pgmb-refresh-preview">
		        <?php esc_html_e('Refresh preview', 'post-to-google-my-business');?>
            </button>
        </div>

        <button type="button" class="button button-primary pgmb-variable-panel-close-button">
            <?php esc_html_e('Close', 'post-to-google-my-business'); ?>
        </button>
	</div>
</div>
