<?php

/*
Plugin Name: Post to Google My Business
Plugin URI: https://tycoonmedia.net
Description: Automatically create a post on Google My Business when creating a new WordPress post
Author: Koen Reus
Version: 3.1.28
Author URI: https://koenreus.com
Text Domain: post-to-google-my-business
*/
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
    exit( sprintf( 'Post to Google My Business requires PHP 7.0 or higher. Your WordPress site is using PHP %s.', PHP_VERSION ) );
}
global $wp_version;
if ( version_compare( $wp_version, '4.9.0', '<' ) ) {
    exit( sprintf( 'Post to Google My Business requires WordPress 4.9.0 or higher. Your WordPress version is %s.', $wp_version ) );
}
if ( function_exists( 'mbp_fs' ) ) {
    // Create a helper function for easy SDK access.
    mbp_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'mbp_fs' ) ) {
        function mbp_fs() {
            global $mbp_fs;
            if ( !isset( $mbp_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_1828_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_1828_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $mbp_fs = fs_dynamic_init( array(
                    'id'              => '1828',
                    'slug'            => 'post-to-google-my-business',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_8ef8aab9dd4277db6bc9b2441830c',
                    'is_premium'      => false,
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                        'days'               => 7,
                        'is_require_payment' => true,
                    ),
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                        'slug' => 'post_to_google_my_business',
                    ),
                    'is_live'         => true,
                ) );
            }
            return $mbp_fs;
        }

    }
    require_once __DIR__ . '/vendor/autoload.php';
    //wp_insert_site is new in WordPress 5.1.0, wpmu_new_blog deprecated
    if ( function_exists( 'wp_initialize_site' ) ) {
        add_action( 'wp_initialize_site', ['\\PGMB\\Plugin', 'insert_site'] );
    } else {
        add_action( 'wpmu_new_blog', ['\\PGMB\\Plugin', 'insert_site'] );
    }
    register_activation_hook( __FILE__, ['\\PGMB\\Plugin', 'activate'] );
    register_deactivation_hook( __FILE__, ['\\PGMB\\Plugin', 'deactivate'] );
    mbp_fs()->add_action( 'after_uninstall', ['\\PGMB\\Plugin', 'uninstall'] );
    mbp_fs()->add_action( 'after_premium_version_activation', ['\\PGMB\\Plugin', 'premium_version_activation'] );
    mbp_fs()->add_action( 'after_free_version_reactivation', ['\\PGMB\\Plugin', 'free_version_reactivation'] );
    $post_to_google_my_business = new \PGMB\Plugin(__FILE__, mbp_fs());
    do_action( 'mbp_fs_loaded' );
    add_action( 'after_setup_theme', [$post_to_google_my_business, 'init'] );
}