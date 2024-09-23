<?php

namespace PGMB\Upgrader;

use PGMB\ParseFormFields;
use PGMB\PostTypes\SubPost;
class Upgrade_2_2_3 implements DistributedUpgrade {
    private $background_process;

    public function task( $post_id ) {
        $this->update_created_posts( $post_id );
        return false;
    }

    public function run() {
        wp_unschedule_hook( 'mbp_schedule_post' );
        //Clear all schedules
        $mbp_posts = get_posts( [
            'numberposts' => -1,
            'post_type'   => SubPost::POST_TYPE,
            'fields'      => 'ids',
        ] );
        foreach ( $mbp_posts as $mbp_post_id ) {
            $this->background_process->push_to_queue( $mbp_post_id, [$this, 'task'] );
        }
        $this->background_process->save()->dispatch();
    }

    private function update_created_posts( $post_id ) {
        $created_posts = get_post_meta( $post_id, 'mbp_posts', true );
        $updated_created_posts = [];
        if ( is_array( $created_posts ) && !empty( $created_posts ) ) {
            foreach ( $created_posts as $index => $created_post ) {
                if ( empty( $created_post['location'] ) || empty( $created_post['url'] ) ) {
                    $updated_created_posts[$index] = $created_post;
                    continue;
                }
                $created_post['searchUrl'] = $created_post['url'];
                $updated_created_posts[$created_post['location']] = $created_post;
            }
            update_post_meta( $post_id, 'mbp_posts', $updated_created_posts );
        }
        $errors = get_post_meta( $post_id, 'mbp_errors', true );
        if ( !$errors || !is_array( $errors ) ) {
            update_post_meta( $post_id, 'mbp_errors', [] );
            //mbp_errors field is needed for dialog
        }
    }

    public function set_background_process( UpgradeBackgroundProcess $upgrader ) {
        $this->background_process = $upgrader;
    }

}
