<?php

if ( $this instanceof \PGMB\Components\PostEditor ) {
    ?>
    <div class="mbp-post-form-container<?php 
    if ( $this->is_ajax_enabled() ) {
        ?> hidden<?php 
    }
    ?>"
         xmlns="http://www.w3.org/1999/html">

        <div class="nav-tab-wrapper current">
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-alert-field'
               data-topic='ALERT'><span
                        class="dashicons dashicons-sos"></span> <?php 
    _e( 'COVID-19 update', 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab nav-tab-active mbp-tab-default" data-fields='mbp-whatsnew-field'
               data-topic='STANDARD'><span
                    class="dashicons dashicons-megaphone"></span> <?php 
    _e( "What's New", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-event-field' data-topic='EVENT'><span
                    class="dashicons dashicons-calendar"></span> <?php 
    _e( "Event", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab" data-fields='mbp-offer-field' data-topic='OFFER'><span
                    class="dashicons dashicons-tag"></span> <?php 
    _e( "Offer", 'post-to-google-my-business' );
    ?>
            </a>
            <a href="#" class="mbp-nav-tab nav-tab pgmb-nav-tab-product" data-fields='mbp-product-field' data-topic='PRODUCT'><span
                    class="dashicons dashicons-cart"></span> <?php 
    _e( "Product", 'post-to-google-my-business' );
    ?>
            </a>
        </div>

        <div class='mbp-tabs-container'>
            <fieldset id='mbp-post-data'>
                <!--			<input type='hidden' name='mbp_attachment_type' class='mbp-hidden' value='PHOTO' />-->
                <?php 
    if ( !$this->is_ajax_enabled() ) {
        ?>
                    <input type="hidden" class="mbp-hidden mbp-attachment-type"
                           value="<?php 
        echo $this->fields['mbp_attachment_type'];
        ?>"/>
                    <input type="hidden" class="mbp-hidden mbp-post-attachment"
                           value="<?php 
        echo $this->fields['mbp_post_attachment'];
        ?>"/>
                <?php 
    }
    ?>

                <input type="hidden" name="<?php 
    echo $this->field_name;
    ?>[mbp_alert_type]" value="<?php 
    echo $this->fields['mbp_alert_type'];
    ?>" />

                <input type='hidden' name='<?php 
    echo $this->field_name;
    ?>[mbp_topic_type]'
                       class='mbp-hidden mbp-topic-type' value="<?php 
    echo $this->fields['mbp_topic_type'];
    ?>"/>

                <table class="form-table mbp-fields">
                    <tbody>

                    <!-- What's new fields -->

                    <?php 
    if ( mbp_fs()->is_free_plan() ) {
        ?>
                    <tr class="mbp-product-field">
                        <td colspan="2">
                            <h3><?php 
        _e( 'Products are a Post to Google My Business Premium feature', 'post-to-google-my-business' );
        ?></h3>
                            <p>
                                <?php 
        _e( 'Because products require a proprietary authentication method, they are not available in the free version of the plugin. Upgrade to <strong>any</strong> of the Premium versions of Post to Google My Business to (automatically) publish your products to your Google Business Profile.', 'post-to-google-my-business' );
        ?>
                            </p>
                            <br />
                            <a class="button button-primary" href="<?php 
        echo mbp_fs()->get_upgrade_url();
        ?>" target="_blank"><?php 
        _e( 'View pricing &amp; buy now &raquo;', 'post-to-google-my-business' );
        ?></a>
                        </td>
                    </tr>
                    <?php 
    }
    ?>
                    <?php 
    if ( class_exists( 'woocommerce' ) ) {
        ?>
                    <tr class="mbp-product-field">
                        <td colspan="2">
                            <p>
                                <?php 
        $link = sprintf( "<a href=\"https://tycoonmedia.net/product-sync-for-gbp/?utm_source=wordpress&utm_medium=plugin&utm_campaign=psfg+prelaunch&utm_content=product+tab\" target='_blank'>%s</a>", __( 'Product Sync for GBP', 'post-to-google-my-business' ) );
        printf( __( 'Using WooCommerce? Check out my new plugin: %s. It makes it super easy to sync your entire WooCommerce product catalog directly to your Google Business Profile.', 'post-to-google-my-business' ), $link );
        ?>
                            </p>
                        </td>
                    </tr>
                    <?php 
    }
    ?>
                    <?php 
    if ( mbp_fs()->is_plan_or_trial( 'starter' ) ) {
        ?>
                        <tr class="mbp-product-field">
                            <td colspan="2">
                                <p>
                                    <strong><?php 
        $link = sprintf( "<a href=\"https://tycoonmedia.net/blog/publish-products-using-cookie-method/\" target='_blank'>%s</a>", __( 'the guide', 'post-to-google-my-business' ) );
        printf( __( 'Additional configuration is required for publishing products. Please check %s.', 'post-to-google-my-business' ), $link );
        ?></strong>
                                </p>
                            </td>
                        </tr>
                    <?php 
    }
    ?>

                    <tr class='mbp-whatsnew-field mbp-event-field mbp-offer-field<?php 
    ?>'
                        id="post-image-container"> <!-- mbp-product-field -->
                        <th><label
                                for='post_image'><?php 
    _e( 'Post image/video', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>

                            <!--							<input type="text" name="mbp_post_attachment" id="meta-image" class="meta_image" />-->
                            <div class="mediaupload_selector">

                            </div>
                            <br/><span
                                class='description'><?php 
    _e( 'JPG or PNG, 720x540px minimum size', 'post-to-google-my-business' );
    ?></span>
                        </td>
                    </tr>

                    <tr class='mbp-whatsnew-field mbp-alert-field mbp-event-field mbp-offer-field'
                        id='post-text-container'>
                        <th><label for='post_text'><?php 
    _e( 'Post text', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>
                            <textarea id='post_text' name='<?php 
    echo $this->field_name;
    ?>[mbp_post_text]'
                                      class='mbp-required pgmb-field-with-counter' rows="8"
                                      style='width:100%' data-maxchars="1500"><?php 
    echo $this->fields['mbp_post_text'];
    ?></textarea>
                            <div
                                class="mbp-text-details"><?php 
    _e( 'Characters:', 'post-to-google-my-business' );
    ?>
                                <span class="mbp-character-count">0</span>/1500
                                - <?php 
    _e( 'Word count:', 'post-to-google-my-business' );
    ?> <span
                                    class="mbp-word-count">0</span></div>
                            <br/><span
                                class='description'>
                                <?php 
    $link = sprintf( "<a href=\"https://docs.digitaldistortion.dev/article/12-using-the-auto-post-feature\" target='_blank'>%s<span style=\"text-decoration: none\" class=\"dashicons dashicons-external\"></span></a>", __( 'See available dynamic tags that you can use', 'post-to-google-my-business' ) );
    /* translators: %s gets replaced by the link to the support page with available dynamic tags that can be used */
    printf( __( 'The text that should appear on your post. Recommended 150-300 characters. 80 characters show in the Google Search results. 1500 characters maximum. %s', 'post-to-google-my-business' ), $link );
    ?>
                            </span>
                        </td>
                    </tr>


                        <!-- Event fields -->
                        <tr class='mbp-event-field hidden' id='event-title-container'>
                            <th><label
                                    for='event_title'><?php 
    _e( 'Event title', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_title' class='mbp-required'
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_event_title]'
                                       value="<?php 
    echo $this->fields['mbp_event_title'];
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden'>
                            <th></th>
                            <td>
                                <label><input type="checkbox" name="<?php 
    echo $this->field_name;
    ?>[mbp_event_all_day]" id="mbp_event_all_day" /> <?php 
    esc_html_e( 'All day event (time will be ignored)', 'post-to-google-my-business' );
    ?></label>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden' id='event-start-date-container'>
                            <th><label
                                    for='event_start_date'><?php 
    _e( 'Start date', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_start_date'
                                       class='mbp-required mbp-validate-date'
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_event_start_date]'
                                       value="<?php 
    echo $this->fields['mbp_event_start_date'];
    ?>"/>
                                <span id="event_start_date_validator" class="mbp-validated-date-display"></span>
                            </td>
                        </tr>
                        <tr class='mbp-event-field mbp-offer-field hidden' id='event-end-date-container'>
                            <th><label
                                    for='event_end_date'><?php 
    _e( 'End date', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='event_end_date'
                                       class='mbp-required mbp-validate-date'
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_event_end_date]'
                                       value="<?php 
    echo $this->fields['mbp_event_end_date'];
    ?>"/>
                                <span id="event_end_date_validator" class="mbp-validated-date-display"></span>
                            </td>
                        </tr>


                        <!-- Offer fields -->
                        <tr class='mbp-offer-field hidden' id='offer-title-container'>
                            <th><label
                                    for='offer_title'><?php 
    _e( 'Offer title', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_title' class='mbp-required'
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_offer_title]'
                                       value="<?php 
    echo $this->fields['mbp_offer_title'];
    ?>"/>
                                <br/><span
                                    class='description'><?php 
    _e( 'Example: 20% off in store or online', 'post-to-google-my-business' );
    ?></span>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-coupon-container'>
                            <th><label
                                    for='offer_coupon'><?php 
    _e( 'Coupon code (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_coupon' class=''
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_offer_coupon]'
                                       value="<?php 
    echo $this->fields['mbp_offer_coupon'];
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-redeemlink-container'>
                            <th><label
                                    for='offer_redeemlink'><?php 
    _e( 'Link to redeem offer (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_redeemlink' class=''
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_offer_redeemlink]'
                                       value="<?php 
    echo $this->fields['mbp_offer_redeemlink'];
    ?>"/>
                            </td>
                        </tr>
                        <tr class='mbp-offer-field hidden' id='offer-terms-container'>
                            <th><label
                                    for='offer_terms'><?php 
    _e( 'Terms and conditions (optional)', 'post-to-google-my-business' );
    ?></label>
                            </th>
                            <td>
                                <input type='text' id='offer_terms' class=''
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_offer_terms]'
                                       value="<?php 
    echo $this->fields['mbp_offer_terms'];
    ?>"/>
                            </td>
                        </tr>


                        <!-- Product fields -->
<!--                        <tr class='mbp-product-field hidden' id='product-name-container'>-->
<!--                            <td colspan="2">-->
<!--                                --><?php 
    //_e( 'The ability to create Product posts has been (temporarily?) removed from the Google My Business API.', 'post-to-google-my-business' );
    ?><!--<br /><br />-->
<!--                                <strong>-->
<!---->
<!---->
<!--                                --><?php 
    //printf(
    //                                        __('Check out %s for (auto-)publishing your WooCommerce products on Google My Business.', 'post-to-google-my-business'),
    //                                        sprintf(
    //                                                '<a href="https://tycoonmedia.net/blog/auto-publish-woocommerce-products-to-google-my-business/" target="_blank">%s</a>',
    //                                                __('this workaround', 'post-to-google-my-business')
    //                                        )
    //                                );
    ?>
<!--                                </strong>-->
<!--                            </td>-->
<!--                        </tr>-->

                        <?php 
    ?>

                    <!-- Button field -->
                    <tr class='mbp-whatsnew-field mbp-alert-field mbp-event-field mbp-offer-field<?php 
    ?>'>
                        <!-- mbp-product-field -->
                        <th><label
                                for='post_text'><?php 
    esc_html_e( 'Add a button (optional)', 'post-to-google-my-business' );
    ?></label>
                        </th>
                        <td>
                            <div class='mbp-button-settings'>
                                <input type='hidden' name='<?php 
    echo $this->field_name;
    ?>[mbp_button]'
                                       id='mbp_button'
                                       value='1' />
                                <select class="mbp-button-type" name="<?php 
    echo $this->field_name;
    ?>[mbp_button_type]">
                                    <option value="" <?php 
    selected( $this->fields['mbp_button_type'], false );
    ?>><?php 
    _e( 'None', 'post-to-google-my-business' );
    ?></option>
	                                <?php 
    ?>
                                    <option value="BOOK" <?php 
    selected( $this->fields['mbp_button_type'], "BOOK" );
    ?>><?php 
    _e( 'Book', 'post-to-google-my-business' );
    ?></option>
                                    <option value="ORDER" <?php 
    selected( $this->fields['mbp_button_type'], "ORDER" );
    ?>><?php 
    _e( 'Order online', 'post-to-google-my-business' );
    ?></option>
                                    <option value="SHOP" <?php 
    selected( $this->fields['mbp_button_type'], "SHOP" );
    ?>><?php 
    _e( 'Buy', 'post-to-google-my-business' );
    ?></option>
                                    <option value="LEARN_MORE" <?php 
    selected( $this->fields['mbp_button_type'], "LEARN_MORE" );
    ?>><?php 
    _e( 'Learn more', 'post-to-google-my-business' );
    ?></option>
                                    <option value="SIGN_UP" <?php 
    selected( $this->fields['mbp_button_type'], "SIGN_UP" );
    ?>><?php 
    _e( 'Sign up', 'post-to-google-my-business' );
    ?></option>
                                    <option value="CALL" <?php 
    selected( $this->fields['mbp_button_type'], "CALL" );
    ?>><?php 
    _e( 'Call now (uses primary phone number of business)', 'post-to-google-my-business' );
    ?></option>
                                </select>
                            </div>

                            <?php 
    /**
                                <div class='mbp-button-settings hidden'>
    
    
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="BOOK" <?php checked( $this->fields['mbp_button_type'], "BOOK" ); ?>> <?php _e( 'Book', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="ORDER"<?php checked( $this->fields['mbp_button_type'], "ORDER" ); ?>> <?php _e( 'Order', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="SHOP"<?php checked( $this->fields['mbp_button_type'], "SHOP" ); ?>> <?php _e( 'Shop', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="LEARN_MORE"<?php checked( $this->fields['mbp_button_type'], "LEARN_MORE" ); ?>> <?php _e( 'Learn more', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="SIGN_UP"<?php checked( $this->fields['mbp_button_type'], "SIGN_UP" ); ?>> <?php _e( 'Sign up', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <label><input class="mbp-button-type" type="radio"
                 name="<?php echo $this->field_name; ?>[mbp_button_type]"
                 value="CALL"<?php checked( $this->fields['mbp_button_type'], "CALL" ); ?>> <?php _e( 'Call now (uses primary phone number of business)', 'post-to-google-my-business' ); ?>
                                </label><br/>
                                <br/><span
           class='description'><?php _e( 'The text that should appear on your button', 'post-to-google-my-business' ); ?></span>
                                </div>
    */
    ?>
                            <div class="mbp-button-settings mbp-button-url hidden">
                                <br />
                                <input type='text' id='button_url'
                                       name='<?php 
    echo $this->field_name;
    ?>[mbp_button_url]' style='width:100%'
                                       data-default="%post_permalink%"
                                       value="<?php 
    echo $this->fields['mbp_button_url'];
    ?>"/>
                                <br/><span
                                        class='description'><?php 
    _e( 'Optional. Where the user should go when clicking the button. Leave at default (%post_permalink%) to send them to your newly created WordPress post.', 'post-to-google-my-business' );
    ?></span>

                            </div>
                        </td>
                    </tr>

                    <tr class='mbp-whatsnew-field mbp-offer-field mbp-alert-field mbp mbp-event-field<?php 
    ?>'>
                        <td colspan="2">
                            <a href='#' class='mbp-toggle-advanced'><?php 
    _e( 'Advanced post settings', 'post-to-google-my-business' );
    ?> &darr;</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class='mbp-advanced-post-settings hidden'>
                    <table class="form-table mbp-fields">
                        <tbody>
                            <tr class='mbp-whatsnew-field mbp-offer-field mbp-event-field<?php 
    ?>'>
                                <th>
                                    <?php 
    _e( 'Image settings', 'post-to-google-my-business' );
    ?>
                                </th>
                                <td>
                                    <label><input type='checkbox'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_content_image]'
                                                  id='mbp_content_image' value='1'
                                                  data-default="" <?php 
    checked( $this->fields['mbp_content_image'] );
    ?> /> <?php 
    _e( 'Fetch image from post content', 'post-to-google-my-business' );
    ?>
                                    </label>
                                    <br/><span
                                        class='description'><?php 
    _e( 'Try to get an image from the post content (when no custom image is set). This takes priority over the featured image.', 'post-to-google-my-business' );
    ?></span><br/><br/>
                                    <label><input type='checkbox'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_featured_image]'
                                                  id='mbp_featured_image' value='1'
                                                  data-default="" <?php 
    checked( $this->fields['mbp_featured_image'] );
    ?> /> <?php 
    _e( 'Use Featured Image as GMB post image', 'post-to-google-my-business' );
    ?>
                                    </label>
                                    <br/><span
                                        class='description'><?php 
    _e( 'Use the Featured Image as GMB Post image (when no custom image is set)', 'post-to-google-my-business' );
    ?></span>
                                </td>
                            </tr>
                            <tr class='mbp-whatsnew-field mbp-alert-field mbp-offer-field mbp-event-field<?php 
    ?>'>
                                <th>
                                    <?php 
    _e( 'Links', 'post-to-google-my-business' );
    ?>
                                </th>
                                <td>
                                    <label><input type='radio'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_link_parsing_mode]'
                                                  value='none'
                                                  <?php 
    checked( $this->fields['mbp_link_parsing_mode'], 'none' );
    ?> /> <?php 
    _e( 'Hide', 'post-to-google-my-business' );
    ?>
                                    </label><br />
                                    <label><input type='radio'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_link_parsing_mode]'
                                                  value='inline'
                                                  <?php 
    checked( $this->fields['mbp_link_parsing_mode'], 'inline' );
    ?> /> <?php 
    _e( 'Inline', 'post-to-google-my-business' );
    ?>
                                    </label><br />
                                    <label><input type='radio'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_link_parsing_mode]'
                                                  value='nextline'
                                                  <?php 
    checked( $this->fields['mbp_link_parsing_mode'], 'nextline' );
    ?> /> <?php 
    _e( 'Next line', 'post-to-google-my-business' );
    ?>
                                    </label><br />
                                    <label><input type='radio'
                                                  name='<?php 
    echo $this->field_name;
    ?>[mbp_link_parsing_mode]'
                                                  value='table'
                                                  <?php 
    checked( $this->fields['mbp_link_parsing_mode'], 'table' );
    ?> /> <?php 
    _e( 'Table of links (at the end of the post)', 'post-to-google-my-business' );
    ?>
                                    </label>
                                    <br/><span
                                            class='description'><?php 
    _e( 'How the plugin should handle links in the content (when using %post_content%)', 'post-to-google-my-business' );
    ?></span>
                                </td>
                            </tr>

                            <?php 
    if ( mbp_fs()->is_free_plan() ) {
        ?>
                                <tr class='mbp-whatsnew-field mbp-alert-field mbp-offer-field mbp-event-field<?php 
        ?>'>
                                    <td colspan='2'>
                                        <br/>
                                        <?php 
        _e( 'Schedule your Google My Business posts, automatically repost them at specified interval, and pick one or more locations to post to. And many more features!' );
        ?>
                                        <br/><br/>
                                        <a target="_blank" class='button-primary'
                                           href="<?php 
        echo mbp_fs()->get_upgrade_url();
        ?>"><?php 
        _e( 'Upgrade now!', 'post-to-google-my-business' );
        ?></a>
                                        <br/><br/>
                                    </td>
                                </tr>
                            <?php 
    }
    ?>

                            <?php 
    ?>
                        </tbody>
                    </table>
                </div>

	            <?php 
    if ( $this->is_ajax_enabled() ) {
        ?>
                <table class="form-table mbp-fields">
                    <tbody>
                        <tr class='mbp-whatsnew-field mbp-offer-field mbp-alert-field mbp mbp-event-field<?php 
        ?>'>
                            <td class="pgmb-editor-action-buttons" colspan="2">
                                <div class="button-group">
                                    <a href='#' class='button button-secondary'
                                       id='mbp-cancel-post'><?php 
        _e( 'Cancel', 'post-to-google-my-business' );
        ?></a>
                                    <button class='button button-secondary'
                                            id='mbp-draft-post'><?php 
        _e( 'Save draft', 'post-to-google-my-business' );
        ?></button>
                                    <!--                <button class='button button-secondary' id='mbp-preview-post'>-->
				                    <?php 
        //_e('Preview', 'post-to-google-my-business');
        ?><!--</button>-->
                                    <button class='button button-primary'
                                            id='mbp-publish-post'><?php 
        _e( 'Publish Now', 'post-to-google-my-business' );
        ?></button>
                                </div>

                            </td>
                        </tr>
                    </tbody>
                </table>
	            <?php 
    }
    ?>
            </fieldset>
        </div>
    </div>
<?php 
}