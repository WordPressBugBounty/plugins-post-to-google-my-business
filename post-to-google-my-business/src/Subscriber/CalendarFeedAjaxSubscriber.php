<?php

namespace PGMB\Subscriber;

use DateTime;
use Exception;
use PGMB\EventManagement\SubscriberInterface;
use PGMB\Plugin;
use PGMB\PostTypes\SubPost;
use PGMB\PostTypes\SubPostRepository;
use PGMB\Util\DateTimeCompat;
use PGMB\Util\MbString;
class CalendarFeedAjaxSubscriber implements SubscriberInterface {
    private $sub_post_repository;

    const NONCE_ACTION = 'calendar_nonce';

    public function __construct( SubPostRepository $repository ) {
        $this->sub_post_repository = $repository;
    }

    public static function get_subscribed_hooks() {
        return [
            'wp_ajax_mbp_get_timegrid_feed'   => 'generate',
            'wp_ajax_pgmb_calendar_post_data' => 'calendar_post_data',
        ];
    }

    public function generate() {
        if ( !wp_verify_nonce( $_REQUEST['nonce'], self::NONCE_ACTION ) || !current_user_can( 'edit_posts' ) ) {
            wp_send_json_error();
        }
        try {
            $start_date = new DateTime($_REQUEST['start'], DateTimeCompat::get_timezone());
            $end_date = new DateTime($_REQUEST['end'], DateTimeCompat::get_timezone());
            $posts = $this->sub_post_repository->between()->start_date( $start_date->getTimestamp() )->end_date( $end_date->getTimestamp() )->limit( -1 )->find();
            $events = $this->prepare_events( $posts );
            wp_send_json( $events );
        } catch ( Exception $e ) {
            //Todo: check how fullcalendar can handle errors
            wp_send_json_error();
        }
    }

    /**
     * @param SubPost[] $posts
     *
     * @return array
     * @throws Exception
     */
    protected function prepare_events( $posts ) {
        $now = new DateTime('now', DateTimeCompat::get_timezone());
        $events = [];
        foreach ( $posts as $post ) {
            $parent_post_id = $post->get_parent();
            $post_date_timestamp = $post->get_post_publish_date_timestamp();
            //get_post_meta($post_id, '_mbp_post_publish_date', true);
            $parsed_form_fields = $post->parsed_form_fields();
            //$posts_have_error = !empty(get_post_meta($post->get_id(), 'mbp_last_error', true)); //Todo: error handling
            $posts_have_error = $post->has_error();
            $post_date = ( new DateTime() )->setTimestamp( $post_date_timestamp )->setTimezone( DateTimeCompat::get_timezone() );
            //			$post_date->setTimestamp($post_date_timestamp);
            //			$post_date->setTimezone(WpDateTimeZone::getWpTimezone());
            $live = $post_date <= $now;
            $events[] = [
                'title'     => get_the_title( $parent_post_id ),
                'start'     => $post_date->format( DateTime::ISO8601 ),
                'end'       => null,
                'url'       => get_edit_post_link( $parent_post_id, false ),
                'color'     => ( $live ? ( $posts_have_error ? '#DE2E30' : '#4CAF50' ) : '#2196F3' ),
                'live'      => (bool) $live,
                'hasError'  => $posts_have_error,
                'repost'    => $parsed_form_fields->is_repost(),
                'topictype' => $parsed_form_fields->get_topic_type(),
                'post_id'   => $post->get_id(),
            ];
        }
        return $events;
    }

    public function calendar_post_data() {
        if ( !wp_verify_nonce( $_REQUEST['nonce'], self::NONCE_ACTION ) || !current_user_can( 'edit_posts' ) ) {
            wp_send_json_error();
        }
        $post_id = (int) $_REQUEST['post_id'];
        $sub_post = $this->sub_post_repository->find_by_id( $post_id );
        $parent_post_link = get_edit_post_link( $sub_post->get_parent(), false );
        $parsed_form_fields = $sub_post->parsed_form_fields();
        $publish_date_timestamp = $sub_post->get_post_publish_date_timestamp();
        $publish_DateTime = ( new DateTime() )->setTimestamp( $publish_date_timestamp )->setTimezone( DateTimeCompat::get_timezone() );
        $topic_types = Plugin::gbp_topic_types();
        ob_start();
        $last_error = get_post_meta( $post_id, 'mbp_last_error', true );
        if ( !empty( $last_error ) ) {
            ?>
            <div class="notice notice-error notice-alt"><p>
	        <?php 
            echo nl2br( esc_html__( "Post contains errors and possibly hasn't been published to one or more locations.\n\nMost recent error:", 'post-to-google-my-business' ) );
            ?>
            <strong><?php 
            echo $last_error;
            ?></strong><br /><br/>
            <?php 
            esc_html_e( 'To check errors per location, press "List created posts" below', 'post-to-google-my-business' );
            ?>
            </p></div><br />
            <?php 
        }
        ?>
            <table>
                <tr><th><?php 
        esc_html_e( 'Parent', 'post-to-google-my-business' );
        ?></th><td><a href="<?php 
        echo $parent_post_link;
        ?>"><?php 
        echo esc_html( get_the_title( $sub_post->get_parent() ) );
        ?></a></td></tr>
                <tr><th><?php 
        esc_html_e( 'Post type', 'post-to-google-my-business' );
        ?></th><td class="pgmb-calendar-post-type"><?php 
        echo $topic_types[$parsed_form_fields->get_topic_type()]['svg'] . $topic_types[$parsed_form_fields->get_topic_type()]['name'];
        ?></td></tr>
                <tr><th><?php 
        esc_html_e( 'Publication date', 'post-to-google-my-business' );
        ?></th><td><?php 
        echo DateTimeCompat::format_date( $publish_DateTime ) . ' ' . DateTimeCompat::format_time( $publish_DateTime );
        ?></td></tr>

	            <?php 
        ?>
                <tr>
                    <th colspan="2"><?php 
        esc_html_e( 'Content', 'post-to-google-my-business' );
        ?></th>
                </tr>
                <tr>
                    <td colspan="2"><i><?php 
        esc_html_e( MbString::strimwidth(
            $parsed_form_fields->get_summary(),
            0,
            100,
            '...'
        ) );
        ?></i></td>
                </tr>
            </table>

            <hr />
			<a href="<?php 
        echo $parent_post_link;
        ?>"><?php 
        esc_html_e( 'Go to parent WordPress post', 'post-to-google-my-business' );
        ?></a> |
            <a href="<?php 
        echo esc_url( add_query_arg( [
            'pgmb_edit_post' => true,
            'pgmb_post_id'   => $post_id,
        ], $parent_post_link ) );
        ?>"><?php 
        esc_html_e( 'Edit', 'post-to-google-my-business' );
        ?></a> |
            <a href="<?php 
        echo esc_url( add_query_arg( [
            'pgmb_list_posts' => true,
            'pgmb_post_id'    => $post_id,
        ], $parent_post_link ) );
        ?>"><?php 
        esc_html_e( 'List created posts', 'post-to-google-my-business' );
        ?></a> |
            <a href="#" class="pgmb-delete-post" data-post_id="<?php 
        echo $post_id;
        ?>"><?php 
        esc_html_e( 'Delete', 'post-to-google-my-business' );
        ?></a>


		<?php 
        $output = ob_get_contents();
        ob_end_clean();
        wp_send_json_success( [
            'post' => $output,
        ] );
    }

}
