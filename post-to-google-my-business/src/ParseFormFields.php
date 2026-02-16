<?php

namespace PGMB;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use PGMB\API\CachedGoogleMyBusiness;
use PGMB\ApiCache\Location;
use PGMB\ApiCache\LocationCacheRepository;
use PGMB\Google\LocalPost;
use PGMB\Google\MediaItem;
use PGMB\Google\NormalizeLocationName;
use PGMB\Placeholders\PostPermalink;
use PGMB\Placeholders\PostVariables;
use PGMB\Placeholders\SiteVariables;
use PGMB\Placeholders\UserVariables;
use PGMB\Placeholders\LocationVariables;
use PGMB\Placeholders\VariableInterface;
use PGMB\Placeholders\WooCommerceVariables;
use PGMB\Util\DateTimeCompat;
use PGMB\Util\UTF16CodeUnitsUtil;
use PGMB\Vendor\Cron\CronExpression;
class ParseFormFields {
    const GBP_IMAGE_WIDTH = 1200;

    const GBP_IMAGE_HEIGHT = 667;

    private $form_fields;

    public function __construct( $form_fields ) {
        if ( !is_array( $form_fields ) ) {
            throw new InvalidArgumentException('ParseFormFields expects Form Fields array');
        }
        $this->form_fields = $form_fields;
    }

    /**
     * Get DateTime object representing the when a post will be first published
     *
     * @return bool|DateTimeInterface|false DateTime when the post is first published, or false when the post isn't scheduled
     * @throws Exception Invalid DateTime
     */
    public function getPublishDateTime() {
        return false;
    }

    public function sanitize() {
        foreach ( $this->form_fields as $name => &$value ) {
            switch ( $name ) {
                case 'mbp_post_text':
                    $value = sanitize_textarea_field( $value );
                    break;
                case 'mbp_selected_location':
                    $this->sanitize_location( $value );
                    break;
                //				case 'mbp_post_attachment':
                //				case 'mbp_button_url':
                //				case 'mbp_offer_redeemlink':
                //					$value = esc_url_raw($value);
                //					break;
                default:
                    $value = sanitize_text_field( $value );
            }
        }
        return $this->form_fields;
    }

    protected function sanitize_location( &$users ) {
        if ( !is_array( $users ) ) {
            $users = [];
            return;
        }
        foreach ( $users as $user_id => $location ) {
            if ( !is_numeric( $user_id ) ) {
                unset($users[$user_id]);
            }
            if ( is_array( $location ) ) {
                $users[$user_id] = array_map( 'sanitize_text_field', $location );
                continue;
            }
            $users[$user_id] = sanitize_text_field( $location );
        }
    }

    /**
     * @param false $autopost Validate autopost template
     */
    public function validate( $autopost = false ) {
    }

    public function is_repost() {
        return isset( $this->form_fields['mbp_repost'] ) && $this->form_fields['mbp_repost'];
    }

    /**
     * Parse the form fields and return a LocalPost object
     *
     * @param Location $location
     * @param $parent_post_id
     *
     * @return LocalPost
     * @throws Exception
     */
    public function getLocalPost( Location $location, $parent_post_id ) : LocalPost {
        if ( !is_numeric( $parent_post_id ) ) {
            throw new InvalidArgumentException('Parent Post ID required for placeholder parsing');
        }
        $placeholder_variables = $this->generate_placeholder_variables( $parent_post_id, $location->api_formatted() );
        $summary = stripslashes( $this->form_fields['mbp_post_text'] );
        $summary = $this->parse_placeholder_variables( $placeholder_variables, $summary );
        $summary = UTF16CodeUnitsUtil::strimwidth(
            (string) $summary,
            0,
            1500,
            "...",
            "UTF-8"
        );
        $topicType = $this->form_fields['mbp_topic_type'];
        //Throw an error when the PRODUCT type is chosen
        if ( $topicType == 'PRODUCT' ) {
            throw new InvalidArgumentException(__( 'Products are not supported in the free version of the plugin. Please choose a different post type in your template.', 'post-to-google-my-business' ));
        }
        $localPost = new LocalPost($location->get_languageCode(), $summary, $topicType);
        //Set alert type
        if ( $topicType === 'ALERT' ) {
            $localPost->setAlertType( $this->form_fields['mbp_alert_type'] );
        }
        //Add image/video
        $mediaItem = $this->get_media_item( $parent_post_id );
        if ( !empty( $mediaItem ) && $topicType !== 'ALERT' ) {
            $localPost->addMediaItem( $mediaItem );
        }
        // mbp_content_image mbp_featured_image
        //Add button
        if ( isset( $this->form_fields['mbp_button'] ) && $this->form_fields['mbp_button'] && $this->form_fields['mbp_button_type'] ) {
            $buttonURL = $this->parse_placeholder_variables( $placeholder_variables, $this->form_fields['mbp_button_url'] );
            $callToAction = new \PGMB\Google\CallToAction($this->form_fields['mbp_button_type'], $buttonURL);
            $localPost->addCallToAction( $callToAction );
        }
        //Add offer
        if ( $topicType == 'OFFER' ) {
            $localPostOffer = new \PGMB\Google\LocalPostOffer($this->form_fields['mbp_offer_coupon'], $this->form_fields['mbp_offer_redeemlink'], $this->form_fields['mbp_offer_terms']);
            $localPost->addLocalPostOffer( $localPostOffer );
        }
        //Add Event (used by Offer too)
        if ( $topicType == 'OFFER' || $topicType == 'EVENT' ) {
            $eventTitle = ( $topicType == 'OFFER' ? $this->form_fields['mbp_offer_title'] : $this->form_fields['mbp_event_title'] );
            //get the appropriate event title
            $eventTitle = $this->parse_placeholder_variables( $placeholder_variables, $eventTitle );
            $startdate = new \DateTime($this->parse_placeholder_variables( $placeholder_variables, $this->form_fields['mbp_event_start_date'] ), DateTimeCompat::get_timezone());
            $enddate = new \DateTime($this->parse_placeholder_variables( $placeholder_variables, $this->form_fields['mbp_event_end_date'] ), DateTimeCompat::get_timezone());
            $startDate = new \PGMB\Google\Date($startdate->format( 'Y' ), $startdate->format( 'm' ), $startdate->format( 'd' ));
            $startTime = new \PGMB\Google\TimeOfDay($startdate->format( 'H' ), $startdate->format( 'i' ));
            $endDate = new \PGMB\Google\Date($enddate->format( 'Y' ), $enddate->format( 'm' ), $enddate->format( 'd' ));
            $endTime = new \PGMB\Google\TimeOfDay($enddate->format( 'H' ), $enddate->format( 'i' ));
            $timeInterval = new \PGMB\Google\TimeInterval(
                $startDate,
                $startTime,
                $endDate,
                $endTime
            );
            if ( isset( $this->form_fields['mbp_event_all_day'] ) && $this->form_fields['mbp_event_all_day'] ) {
                $timeInterval->setAllDay( true );
            }
            $eventTitle = UTF16CodeUnitsUtil::strimwidth( (string) $eventTitle, 0, 58 );
            $localPostEvent = new \PGMB\Google\LocalPostEvent($eventTitle, $timeInterval);
            $localPost->addLocalPostEvent( $localPostEvent );
        }
        return $localPost;
    }

    public function get_media_items( $parent_post_id ) {
        $mediaItems = [];
        if ( empty( $this->form_fields['mbp_post_attachment'] ) || !is_array( $this->form_fields['mbp_post_attachment'] ) ) {
            return false;
        }
        foreach ( $this->form_fields['mbp_post_attachment'] as $type => $items ) {
            foreach ( $items as $item ) {
                $mediaItems[] = new MediaItem($type, $item);
            }
        }
        return $mediaItems;
    }

    public function get_media_item( $parent_post_id ) {
        // If the post has a custom image set
        if ( !empty( $this->form_fields['mbp_post_attachment'] ) ) {
            $image_id = attachment_url_to_postid( $this->form_fields['mbp_post_attachment'] );
            if ( $image_id && wp_attachment_is_image( $image_id ) ) {
                $url = $this->validate_wp_image_size( $image_id );
            } else {
                $url = $this->validate_external_image_size( $this->form_fields['mbp_post_attachment'] );
            }
            return new \PGMB\Google\MediaItem($this->form_fields['mbp_attachment_type'], $url);
            // If "Fetch image from content" is enabled
        } elseif ( isset( $this->form_fields['mbp_content_image'] ) && $this->form_fields['mbp_content_image'] && ($image_url = $this->get_content_image( $parent_post_id )) ) {
            return new \PGMB\Google\MediaItem('PHOTO', $image_url);
            // If "Use featured image" is enabled
        } elseif ( isset( $this->form_fields['mbp_featured_image'] ) && $this->form_fields['mbp_featured_image'] && get_the_post_thumbnail_url( $parent_post_id, 'pgmb-post-image' ) ) {
            $image_id = get_post_thumbnail_id( $parent_post_id );
            $image_url = $this->validate_wp_image_size( $image_id );
            return new \PGMB\Google\MediaItem('PHOTO', $image_url);
        }
        return false;
    }

    public function get_content_image( $post_id ) {
        $images = get_attached_media( 'image', $post_id );
        if ( !($image = reset( $images )) ) {
            return false;
        }
        //wp_get_attachment_image_src($image->ID, 'pgmb-post-image');
        return $this->validate_wp_image_size( $image->ID );
    }

    public function is_url_relative( $url ) {
        return \strpos( $url, 'http' ) !== 0 && \strpos( $url, '//' ) !== 0;
    }

    public function ensure_absolute_url( $url ) {
        if ( $this->is_url_relative( $url ) ) {
            $parsed_home_url = parse_url( home_url() );
            $url = $parsed_home_url['scheme'] . '://' . $parsed_home_url['host'] . $url;
        }
        return $url;
    }

    /**
     * Try to generate the "pgmb-post-image" intermediate image file for the image ID
     *
     * Returns "full" image size if it is already smaller than 1200x900
     * Returns originally uploaded image file if intermediate generation fails
     * Returns intermediate file on success
     *
     * @param int $image_id WordPress Attachment ID
     *
     * @return array
     *
     * @throws Exception When the original image file doesn't exist
     */
    function maybe_generate_intermediate( int $image_id ) : array {
        list( $url, $width, $height ) = wp_get_attachment_image_src( $image_id, 'full' );
        if ( $width <= 1200 && $height <= 900 ) {
            $path = get_attached_file( $image_id );
            return [
                $url,
                $path,
                $width,
                $height
            ];
        }
        if ( !function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $path = wp_get_original_image_path( $image_id );
        if ( !$path || !file_exists( $path ) ) {
            throw new Exception(__( 'Original image file not found', 'post-to-google-my-business' ));
        }
        wp_generate_attachment_metadata( $image_id, $path );
        $intermediate = image_get_intermediate_size( $image_id, 'pgmb-post-image' );
        if ( !$intermediate ) {
            //			throw new Exception(__('Could not generate intermediate image file size', 'post-to-google-my-business'));
            //bail and return the original image
            return [
                $url,
                $path,
                $width,
                $height
            ];
        }
        $uploads_dir = wp_get_upload_dir();
        return [
            $intermediate['url'],
            $uploads_dir['basedir'] . "/" . $intermediate['path'],
            $intermediate['width'],
            $intermediate['height']
        ];
    }

    /**
     * Check whether a file uploaded within the WordPress Media Uploader matches the requirements for GMB
     *
     * @param int $image_id - ID of the image within WordPress
     *
     * @throws Exception
     */
    public function validate_wp_image_size( $image_id ) {
        $path = get_attached_file( $image_id );
        list( $url, $width, $height, $is_intermediate ) = wp_get_attachment_image_src( $image_id, 'pgmb-post-image' );
        if ( !$is_intermediate ) {
            list( $url, $path, $width, $height ) = $this->maybe_generate_intermediate( $image_id );
        }
        if ( $this->is_image_webp_or_avif( $path, $url ) ) {
            if ( $this->original_upload_matches_gmb_requirements( $image_id ) ) {
                list( $url, $path, $width, $height ) = $this->get_original_upload( $image_id );
            } else {
                list( $path, $url ) = $this->convert_image_if_needed( $image_id, $path, $url );
            }
        }
        $url = $this->ensure_absolute_url( $url );
        $image_file_size = $this->get_local_file_size( $path, $url );
        if ( !$image_file_size ) {
            /* translators: %s is image url */
            throw new InvalidArgumentException(sprintf( __( 'Could not detect post image file size. Make sure the image file/url is accessible remotely. Url: %s', 'post-to-google-my-business' ), esc_url( $url ) ));
        }
        $this->validate_image_props(
            $url,
            $image_file_size,
            $width,
            $height
        );
        return $url;
    }

    private function get_original_upload( $image_id ) {
        $path = wp_get_original_image_path( $image_id );
        $url = wp_get_original_image_url( $image_id );
        if ( !$path || !file_exists( $path ) || !$url ) {
            return false;
        }
        //Available from WordPress 5.7
        if ( function_exists( 'wp_getimagesize' ) ) {
            $image_data = wp_getimagesize( $path );
        } else {
            $image_data = getimagesize( $path );
        }
        if ( !$image_data ) {
            return false;
        }
        list( $width, $height ) = $image_data;
        return [
            $url,
            $path,
            $width,
            $height
        ];
    }

    private function convert_image_if_needed( $image_id, $path, $url ) {
        if ( wp_get_image_mime( $path ) == 'image/webp' || $this->is_remote_mime_webp( $url ) ) {
            return $this->convert_webp( $image_id );
        } elseif ( wp_get_image_mime( $path ) == 'image/avif' || $this->is_remote_mime_avif( $url ) ) {
            return $this->convert_avif( $image_id );
        }
        return [$path, $url];
    }

    private function original_upload_matches_gmb_requirements( $image_id ) {
        $original_upload = $this->get_original_upload( $image_id );
        if ( !$original_upload ) {
            return false;
        }
        list( $url, $path, $width, $height ) = $original_upload;
        $image_file_size = $this->get_local_file_size( $path, $url );
        if ( !$image_file_size ) {
            return false;
        }
        //If the originally uploaded image is also webp or avif
        if ( $this->is_image_webp_or_avif( $path, $url ) ) {
            return false;
        }
        try {
            $this->validate_image_props(
                $url,
                $image_file_size,
                $width,
                $height
            );
        } catch ( Exception $e ) {
            return false;
        }
        return true;
    }

    private function is_image_webp_or_avif( $path, $url ) {
        return wp_get_image_mime( $path ) == 'image/webp' || $this->is_remote_mime_webp( $url ) || wp_get_image_mime( $path ) == 'image/avif' || $this->is_remote_mime_avif( $url );
    }

    public function is_remote_mime_webp( $url ) {
        $headers = wp_get_http_headers( $url );
        return isset( $headers['Content-Type'] ) && $headers['Content-Type'] === 'image/webp';
    }

    public function is_remote_mime_avif( $url ) {
        $headers = wp_get_http_headers( $url );
        return isset( $headers['Content-Type'] ) && $headers['Content-Type'] === 'image/avif';
    }

    /**
     * Get file size in bytes from a file on the local server
     *
     * @param $path
     *
     * @return false|int
     */
    public function get_file_size_from_path( $path ) {
        return @filesize( $path );
    }

    /**
     * Try to determine file size by getting content-length from headers (not always available)
     *
     * @param $url
     *
     * @return bool|int
     */
    public function get_file_size_from_headers( $url ) {
        $headers = wp_get_http_headers( $url );
        if ( !$headers || !isset( $headers['content-length'] ) ) {
            return false;
        }
        return intval( $headers['content-length'] );
    }

    /**
     * Try to determine the file size by downloading the file
     *
     * @param $url
     *
     * @return bool|false|int
     */
    public function get_file_size_from_download( $url ) {
        if ( !function_exists( 'download_url' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        $filepath = download_url( $url );
        if ( is_wp_error( $filepath ) ) {
            return false;
        }
        $file_size = filesize( $filepath );
        unlink( $filepath );
        return $file_size;
    }

    public function get_local_file_size( $path, $url ) {
        $image_file_size = $this->get_file_size_from_path( $path );
        if ( $image_file_size ) {
            return $image_file_size;
        }
        $image_file_size = $this->get_remote_file_size( $url );
        if ( $image_file_size ) {
            return $image_file_size;
        }
        return false;
    }

    public function get_remote_file_size( $url ) {
        $image_file_size = $this->get_file_size_from_headers( $url );
        if ( $image_file_size ) {
            return $image_file_size;
        }
        $image_file_size = $this->get_file_size_from_download( $url );
        if ( $image_file_size ) {
            return $image_file_size;
        }
        return false;
    }

    /**
     * Check if externally hosted image meets the GMB requirements
     *
     * @param string $url - URL of the image
     */
    public function validate_external_image_size( $url ) {
        list( $width, $height ) = getimagesize( $url );
        $image_file_size = $this->get_remote_file_size( $url );
        if ( !$image_file_size ) {
            /* translators: %s is image url */
            throw new InvalidArgumentException(sprintf( __( 'Could not detect post image file size. Make sure the image file/url is accessible remotely. Url: %s', 'post-to-google-my-business' ), esc_url( $url ) ));
        }
        $this->validate_image_props(
            $url,
            $image_file_size,
            $width,
            $height
        );
        return $url;
    }

    public function validate_image_props(
        $url,
        $image_file_size,
        $width,
        $height
    ) {
        if ( $width < 250 || $height < 250 ) {
            /* translators: %1$dx%2$dpx is the size of the failed image (width x height), %3$s is image url */
            throw new InvalidArgumentException(sprintf(
                __( 'Post image must be at least 250x250px. Selected image is %1$dx%2$dpx. Url: %3$s', 'post-to-google-my-business' ),
                $width,
                $height,
                esc_url( $url )
            ));
        }
        if ( $image_file_size < 10240 ) {
            /* translators: %1$s is formatted image size, %2$s is image URL */
            throw new InvalidArgumentException(sprintf( __( 'Post image file too small, must be at least 10 KB. Selected image is %1$s. Url: %2$s', 'post-to-google-my-business' ), size_format( $image_file_size ), esc_url( $url ) ));
        } elseif ( $image_file_size > 5242880 ) {
            /* translators: %1$s is formatted image size, %2$s is image URL */
            throw new InvalidArgumentException(sprintf( __( 'Post image file too big, must be 5 MB at most. Selected image is %1$s. Url: %2$s', 'post-to-google-my-business' ), size_format( $image_file_size ), esc_url( $url ) ));
        }
    }

    public function convert_webp( $image_id ) {
        $path = get_attached_file( $image_id );
        if ( !function_exists( 'imagecreatefromwebp' ) ) {
            throw new \RuntimeException(__( 'Tried to convert WebP image but imagecreatefromwebp is not available' ));
        }
        $filename = 'pgmb_' . time() . '.png';
        $image = imagecreatefromwebp( $path );
        $wp_upload_dir = wp_upload_dir();
        $new_path = trailingslashit( $wp_upload_dir['path'] ) . $filename;
        $url = trailingslashit( $wp_upload_dir['url'] ) . $filename;
        list( $new_path, $url ) = $this->create_and_maybe_resize_and_crop_image( $image, $new_path, $url );
        imagedestroy( $image );
        return [$new_path, $url];
    }

    public function convert_avif( $image_id ) {
        $path = get_attached_file( $image_id );
        $filename = 'pgmb_' . time() . '.png';
        $wp_upload_dir = wp_upload_dir();
        $new_path = trailingslashit( $wp_upload_dir['path'] ) . $filename;
        $url = trailingslashit( $wp_upload_dir['url'] ) . $filename;
        // Check if imagecreatefromavif is available (PHP 8)
        if ( function_exists( 'imagecreatefromavif' ) ) {
            try {
                $image = imagecreatefromavif( $path );
                if ( !$image ) {
                    throw new \RuntimeException(__( 'Failed to create image from AVIF using imagecreatefromavif' ));
                }
                list( $new_path, $url ) = $this->create_and_maybe_resize_and_crop_image( $image, $new_path, $url );
                imagedestroy( $image );
                return [$new_path, $url];
            } catch ( Exception $e ) {
                // Fall back on Imagick if imagecreatefromavif fails
                return $this->convert_avif_with_imagick( $path, $new_path, $url );
            }
        } else {
            /*
             * Fall back on Imagick if imagecreatefromavif is not available
             *
             * Note that this fallback is only needed for avif and not webp because the equivalent webp feature has
             * been available since php 5.4
             */
            return $this->convert_avif_with_imagick( $path, $new_path, $url );
        }
    }

    private function convert_avif_with_imagick( $path, $new_path, $url ) {
        if ( !extension_loaded( 'imagick' ) ) {
            throw new \RuntimeException(__( 'Tried to convert AVIF image but neither imagecreatefromavif nor Imagick is available' ));
        }
        try {
            $imagick = new \Imagick($path);
            $imagick->setImageFormat( 'png' );
            $imagick->stripImage();
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            //Return if the image is already smaller than the recommended values
            if ( $width <= self::GBP_IMAGE_WIDTH && $height <= self::GBP_IMAGE_HEIGHT ) {
                $imagick->writeImage( $new_path );
                $imagick->clear();
                return [$new_path, $url];
            }
            $imagick->resizeImage(
                self::GBP_IMAGE_WIDTH,
                0,
                \Imagick::FILTER_LANCZOS,
                1,
                true
            );
            $resized_height = $imagick->getImageHeight();
            if ( $resized_height > self::GBP_IMAGE_HEIGHT ) {
                $crop_y = (int) floor( ($resized_height - self::GBP_IMAGE_HEIGHT) / 2 );
                $imagick->cropImage(
                    self::GBP_IMAGE_WIDTH,
                    self::GBP_IMAGE_HEIGHT,
                    0,
                    $crop_y
                );
                $imagick->setImagePage(
                    0,
                    0,
                    0,
                    0
                );
            }
            $imagick->writeImage( $new_path );
            $imagick->clear();
            return [$new_path, $url];
        } catch ( Exception $e ) {
            throw new \RuntimeException(__( 'AVIF image conversion failed: ' ) . $e->getMessage());
        }
    }

    /**
     * This will take a GdImage and turn it into a png file
     *
     * Will crop/resize if the image is larger than recommended Google size
     *
     * @param resource|\GdImage $image
     * @param $new_path
     * @param $url
     *
     * @return array
     */
    private function create_and_maybe_resize_and_crop_image( $image, $new_path, $url ) : array {
        $width = imagesx( $image );
        $height = imagesy( $image );
        if ( $width <= self::GBP_IMAGE_WIDTH && $height <= self::GBP_IMAGE_HEIGHT ) {
            imagepng( $image, $new_path );
            imagedestroy( $image );
            return [$new_path, $url];
        }
        $scale = self::GBP_IMAGE_WIDTH / $width;
        $scaled_height = (int) round( $height * $scale );
        $resized = imagecreatetruecolor( self::GBP_IMAGE_WIDTH, $scaled_height );
        imagecopyresampled(
            $resized,
            $image,
            0,
            0,
            0,
            0,
            self::GBP_IMAGE_WIDTH,
            $scaled_height,
            $width,
            $height
        );
        if ( $scaled_height > self::GBP_IMAGE_HEIGHT ) {
            $crop_y = (int) floor( ($scaled_height - self::GBP_IMAGE_HEIGHT) / 2 );
            $final = imagecreatetruecolor( self::GBP_IMAGE_WIDTH, self::GBP_IMAGE_HEIGHT );
            imagecopy(
                $final,
                $resized,
                0,
                0,
                0,
                $crop_y,
                self::GBP_IMAGE_WIDTH,
                self::GBP_IMAGE_HEIGHT
            );
        } else {
            $final = $resized;
        }
        imagepng( $final, $new_path );
        if ( $final !== $resized ) {
            imagedestroy( $resized );
        }
        imagedestroy( $final );
        return [$new_path, $url];
    }

    public function get_topic_type() {
        return $this->form_fields['mbp_topic_type'];
    }

    public function get_summary() {
        if ( mbp_fs()->is__premium_only() && $this->get_topic_type() == 'PRODUCT' ) {
            return (string) $this->form_fields['mbp_product_description'];
        }
        return (string) $this->form_fields['mbp_post_text'];
    }

    /**
     * Get array of locations to post to. Return default location if nothing is selected
     *
     * @param $default_location
     *
     * @return array Locations to post to
     */
    public function getLocations( $default_location ) {
        if ( !isset( $this->form_fields['mbp_selected_location'] ) || empty( $this->form_fields['mbp_selected_location'] ) ) {
            return $default_location;
        }
        if ( !is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return [$this->form_fields['mbp_selected_location']];
        } elseif ( is_array( $this->form_fields['mbp_selected_location'] ) ) {
            return $this->form_fields['mbp_selected_location'];
        }
        throw new \UnexpectedValueException(__( "Could not parse post locations", 'post-to-google-my-business' ));
    }

    public function get_link_parsing_mode() {
        $valid_modes = [
            'none',
            'inline',
            'nextline',
            'table'
        ];
        if ( !isset( $this->form_fields['mbp_link_parsing_mode'] ) || !in_array( $this->form_fields['mbp_link_parsing_mode'], $valid_modes ) ) {
            return 'inline';
        }
        return $this->form_fields['mbp_link_parsing_mode'];
    }

    public function generate_placeholder_variables( $parent_post_id, $location ) {
        $decorators = [
            'post_permalink'        => new PostPermalink($parent_post_id),
            'post_variables'        => new PostVariables($parent_post_id, $this->get_link_parsing_mode()),
            'user_variables'        => new UserVariables($parent_post_id),
            'site_variables'        => new SiteVariables(),
            'location_variables'    => new LocationVariables($location),
            'woocommerce_variables' => new WooCommerceVariables($parent_post_id, $this->get_link_parsing_mode()),
        ];
        $decorators = apply_filters(
            'mbp_placeholder_decorators',
            $decorators,
            $parent_post_id,
            $location
        );
        $variables = [];
        foreach ( $decorators as $decorator ) {
            if ( $decorator instanceof VariableInterface ) {
                $variables = array_merge( $variables, $decorator->variables() );
            }
        }
        return apply_filters( 'mbp_placeholder_variables', $variables, $parent_post_id );
    }

    public function parse_placeholder_variables( $variables, $text ) {
        return str_replace( array_keys( $variables ), $variables, $text );
    }

}
