<?php if($this instanceof \PGMB\Metabox\PostCreationMetabox) : ?>
    <div class='mbp-error-notice'>

    </div>
    <div class='mbp-table-head'>
        <?php if($this->is_autopost_enabled()) : ?>
            <input type="hidden" value="1" name="mbp_wp_post" /> <!-- Hidden value so we can determine if the page was submitted without checking the checkbox -->
            <div class="button-group">
                <a href="#" class="button button-secondary"  id='mbp-edit-post-template'><?php _e('Edit auto-post template', 'post-to-google-my-business'); ?></a>
                <a href='#' class='button button-primary' id='mbp-new-post'>+ <?php _e('New GMB Post', 'post-to-google-my-business'); ?></a>
            </div>
        <?php else: ?>
            <a href='#' class='button button-primary' id='mbp-new-post'>+ <?php _e('New GMB Post', 'post-to-google-my-business'); ?></a>
        <?php endif; ?>
    </div>

    <?php echo $this->get_post_editor()->generate(); ?>

<?php /**
<!--    <table class="widefat fixed striped mbp-existing-posts" cellspacing="0">-->
<!--        <thead>-->
<!--            <tr>-->
<!--                <!--<th id="cb" class="manage-column column-cb check-column" scope="col"></th>-->-->
<!--                <th class="manage-column column-posttype" scope="col">--><?php //_e('Post type', 'post-to-google-my-business'); ?><!--</th>-->
<!--                <th class="manage-column column-postdate" scope="col">--><?php //_e('Publish date', 'post-to-google-my-business'); ?><!--</th>-->
<!--                <th class="manage-column column-postcreated" scope="col">--><?php //_e('Created', 'post-to-google-my-business'); ?><!--</th>-->
<!--            </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--            --><?php //$this->get_existing_posts($post->ID); ?>
<!--        </tbody>-->
<!--    </table>-->

 */
?>

<!--    <input type="hidden" name="page" value="--><?php //echo $_REQUEST['page']; ?><!--" />-->
<!--    <input type="hidden" name="order" value="--><?php //echo $_REQUEST['order']; ?><!--" />-->
<!--    <input type="hidden" name="orderby" value="--><?php //echo $_REQUEST['orderby']; ?><!--" />-->

    <div id="pgmb-subpost-table-container" style="">
        <?php
        wp_nonce_field( 'pgmb_subpost_table_fetch', 'pgmb_subpost_table_nonce'  );
        ?>
    </div>


    <br />

    <div id="mbp-created-post-dialog" class="hidden">
        <div id="pgmb-entity-table-container" style="">

        </div>
    </div>
<?php endif; ?>
