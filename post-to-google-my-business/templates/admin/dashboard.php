<?php use PGMB\Admin\DashboardPage;

if( $this instanceof \PGMB\Admin\DashboardPage) : ?>

    <div class="wrap mbp-settings">

        <h2><?php esc_html_e('Post to Google My Business dashboard', 'post-to-google-my-business'); ?></h2>
        <div class="metabox-holder">
            <?php if($this->notification_manager->notification_count('dashboard-notifications')) : ?>
                <div class="pgmb-widget postbox pgmb-notifications-container">
                    <h2 class="hndle ui-sortable-handle">
                        <span>
                            <?php
                                /* translators: %s is the amount of notifications in a styled html component */
                                echo sprintf(esc_html__("Notifications (%s)", "post-to-google-my-business"), '<span class="mbp-notification-count">'.$this->notification_manager->notification_count('dashboard-notifications').'</span>');
                            ?>
                        </span>
                    </h2>

                    <div class="pgmb-widget-inside inside">
                        <?php $this->get_notifications(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($this->notification_manager->notification_count('feature-notifications')) : ?>
            <div class="pgmb-widget postbox pgmb-notifications-container">

                <h2 class="hndle ui-sortable-handle">
                    <span>
                        <?php
                            /* translators: %s is the amount of new features (count) in a styled html component */
                            echo sprintf(esc_html__("New features (%s)", "post-to-google-my-business"), '<span class="mbp-notification-count">'.$this->notification_manager->notification_count('feature-notifications').'</span>');
                        ?>
                    </span>
                </h2>

                <div class="pgmb-widget-inside inside">
                    <div class="pgmb-features-container">
                        <?php $this->get_new_features(); ?>
                    </div>

                </div>
            </div>
            <?php endif; ?>

        <!--    <div class="pgmb-widget postbox">-->
        <!--        <h2 class="hndle ui-sortable-handle"><span>Statistics</span></h2>-->
        <!--        <div class="pgmb-widget-inside inside">-->
        <!---->
        <!--        </div>-->
        <!--    </div>-->
            <div class="pgmb-widget postbox">
                <h2 class="hndle ui-sortable-handle"><span><?php esc_html_e('Calendar', 'post-to-google-my-business'); ?></span></h2>
                <div class="pgmb-widget-inside inside">
                    <div id="pgmb-calender-loading" style="display:none">
                        <span class="spinner is-active"></span>
                    </div>
                    <div id="pgmb-calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="pgmb-calendar-post-popup" class="hidden">
        <div id="pgmb-calendar-post-popup-inner">
        </div>
    </div>
<?php endif; ?>
