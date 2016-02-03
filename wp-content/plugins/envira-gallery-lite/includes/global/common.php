<?php
/**
 * Common class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Common_Lite {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Envira_Gallery_Lite::get_instance();

    }

    /**
     * Helper method for retrieving columns.
     *
     * @since 1.0.0
     *
     * @return array Array of column data.
     */
    public function get_columns() {

        $columns = array(
            array(
                'value' => '1',
                'name'  => __( 'One Column (1)', 'envira-gallery-lite' )
            ),
            array(
                'value' => '2',
                'name'  => __( 'Two Columns (2)', 'envira-gallery-lite' )
            ),
            array(
                'value' => '3',
                'name'  => __( 'Three Columns (3)', 'envira-gallery-lite' )
            ),
            array(
                'value' => '4',
                'name'  => __( 'Four Columns (4)', 'envira-gallery-lite' )
            ),
            array(
                'value' => '5',
                'name'  => __( 'Five Columns (5)', 'envira-gallery-lite' )
            ),
            array(
                'value' => '6',
                'name'  => __( 'Six Columns (6)', 'envira-gallery-lite' )
            )
        );

        return apply_filters( 'envira_gallery_columns', $columns );

    }

    /**
     * Helper method for retrieving gallery themes.
     *
     * @since 1.0.0
     *
     * @return array Array of gallery theme data.
     */
    public function get_gallery_themes() {

        $themes = array(
            array(
                'value' => 'base',
                'name'  => __( 'Base', 'envira-gallery-lite' ),
                'file'  => $this->base->file
            )
        );

        return apply_filters( 'envira_gallery_gallery_themes', $themes );

    }

    /**
     * Helper method for retrieving lightbox themes.
     *
     * @since 1.0.0
     *
     * @return array Array of lightbox theme data.
     */
    public function get_lightbox_themes() {

        $themes = array(
            array(
                'value' => 'base',
                'name'  => __( 'Base', 'envira-gallery-lite' ),
                'file'  => $this->base->file
            )
        );

        return apply_filters( 'envira_gallery_lightbox_themes', $themes );

    }

    /**
     * Helper method for retrieving title displays.
     *
     * @since 1.2.7
     *
     * @return array Array of title display data.
     */
    public function get_title_displays() {

        $displays = array(
            array(
                'value' => 'float',
                'name'  => __( 'Float', 'envira-gallery-lite' )
            ),
            array(
                'value' => 'float_wrap',
                'name'  => __( 'Float (Wrapped)', 'envira-gallery-lite' )
            ),
        );

        return apply_filters( 'envira_gallery_title_displays', $displays );

    }

    /**
     * Helper method for setting default config values.
     *
     * @since 1.0.0
     *
     * @global int $id      The current post ID.
     * @global object $post The current post object.
     * @param string $key   The default config key to retrieve.
     * @return string       Key value on success, false on failure.
     */
    public function get_config_default( $key ) {

        global $id, $post;

        // Get the current post ID.
        $post_id = ( null === $id ) ? $post->ID : $id;

        // Prepare default values.
        $defaults = array(
            'columns'             => '1',
            'gallery_theme'       => 'base',
            'lightbox_theme'      => 'base',
            'title_display'       => 'float',
            'gutter'              => 10,
            'margin'              => 10,
            'crop'                => 0,
            'crop_width'          => 960,
            'crop_height'         => 300,
            'lightbox_enabled'    => 1,
            'arrows'              => 1,
            'keyboard'            => 1,
            'mousewheel'          => 1,
            'aspect'              => 1,
            'toolbar'             => 0,
            'toolbar_position'    => 'top',
            'loop'                => 1,
            'classes'             => array(),
            'title'               => '',
            'slug'                => '',
            'rtl'                 => 0,
        );

        // Allow devs to filter the defaults.
        $defaults = apply_filters( 'envira_gallery_defaults', $defaults, $post_id );

        // Return the key specified.
        return isset( $defaults[$key] ) ? $defaults[$key] : false;

    }

    /**
     * API method for cropping images.
     *
     * @since 1.0.0
     *
     * @global object $wpdb The $wpdb database object.
     *
     * @param string $url      The URL of the image to resize.
     * @param int $width       The width for cropping the image.
     * @param int $height      The height for cropping the image.
     * @param bool $crop       Whether or not to crop the image (default yes).
     * @param string $align    The crop position alignment.
     * @param bool $retina     Whether or not to make a retina copy of image.
     * @return WP_Error|string Return WP_Error on error, URL of resized image on success.
     */
    public function resize_image( $url, $width = null, $height = null, $crop = true, $align = 'c', $quality = 100, $retina = false ) {

        global $wpdb;

        // Get common vars.
        $args   = func_get_args();
        $common = $this->get_image_info( $args );

        // Unpack variables if an array, otherwise return WP_Error.
        if ( is_wp_error( $common ) ) {
            return $common;
        } else {
            extract( $common );
        }

        // If the destination width/height values are the same as the original, don't do anything.
        if ( $orig_width === $dest_width && $orig_height === $dest_height ) {
            return $url;
        }

        // If the file doesn't exist yet, we need to create it.
        if ( ! file_exists( $dest_file_name ) ) {
            // We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
            $get_attachment = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE guid='%s'", $url ) );

            // Load the WordPress image editor.
            $editor = wp_get_image_editor( $file_path );

            // If an editor cannot be found, the user needs to have GD or Imagick installed.
            if ( is_wp_error( $editor ) ) {
                return new WP_Error( 'envira-gallery-error-no-editor', __( 'No image editor could be selected. Please verify with your webhost that you have either the GD or Imagick image library compiled with your PHP install on your server.', 'envira-gallery-lite' ) );
            }

            // Set the image editor quality.
            $editor->set_quality( $quality );

            // If cropping, process cropping.
            if ( $crop ) {
                $src_x = $src_y = 0;
                $src_w = $orig_width;
                $src_h = $orig_height;

                $cmp_x = $orig_width / $dest_width;
                $cmp_y = $orig_height / $dest_height;

                // Calculate x or y coordinate and width or height of source
                if ( $cmp_x > $cmp_y ) {
                    $src_w = round( $orig_width / $cmp_x * $cmp_y );
                    $src_x = round( ($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2 );
                } else if ( $cmp_y > $cmp_x ) {
                    $src_h = round( $orig_height / $cmp_y * $cmp_x );
                    $src_y = round( ($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2 );
                }

                // Positional cropping.
                if ( $align && $align != 'c' ) {
                    if ( strpos( $align, 't' ) !== false || strpos( $align, 'tr' ) !== false || strpos( $align, 'tl' ) !== false ) {
                        $src_y = 0;
                    }

                    if ( strpos( $align, 'b' ) !== false || strpos( $align, 'br' ) !== false || strpos( $align, 'bl' ) !== false ) {
                        $src_y = $orig_height - $src_h;
                    }

                    if ( strpos( $align, 'l' ) !== false ) {
                        $src_x = 0;
                    }

                    if ( strpos ( $align, 'r' ) !== false ) {
                        $src_x = $orig_width - $src_w;
                    }
                }

                // Crop the image.
                $editor->crop( $src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height );
            } else {
                // Just resize the image.
                $editor->resize( $dest_width, $dest_height );
            }

            // Save the image.
            $saved = $editor->save( $dest_file_name );

            // Print possible out of memory errors.
            if ( is_wp_error( $saved ) ) {
                @unlink( $dest_file_name );
                return $saved;
            }

            // Add the resized dimensions and alignment to original image metadata, so the images
            // can be deleted when the original image is delete from the Media Library.
            if ( $get_attachment ) {
                $metadata = wp_get_attachment_metadata( $get_attachment[0]->ID );

                if ( isset( $metadata['image_meta'] ) ) {
                    $md = $saved['width'] . 'x' . $saved['height'];

                    if ( $crop ) {
                        $md .= $align ? "_${align}" : "_c";
                    }

                    $metadata['image_meta']['resized_images'][] = $md;
                    wp_update_attachment_metadata( $get_attachment[0]->ID, $metadata );
                }
            }

            // Set the resized image URL.
            $resized_url = str_replace( basename( $url ), basename( $saved['path'] ), $url );
        } else {
            // Set the resized image URL.
            $resized_url = str_replace( basename( $url ), basename( $dest_file_name ), $url );
        }

        // Return the resized image URL.
        return $resized_url;

    }

    /**
     * Helper method to return common information about an image.
     *
     * @since 1.0.0
     *
     * @param array $args      List of resizing args to expand for gathering info.
     * @return WP_Error|string Return WP_Error on error, array of data on success.
     */
    public function get_image_info( $args ) {

        // Unpack arguments.
        list( $url, $width, $height, $crop, $align, $quality, $retina ) = $args;

        // Return an error if no URL is present.
        if ( empty( $url ) ) {
            return new WP_Error( 'envira-gallery-error-no-url', __( 'No image URL specified for cropping.', 'envira-gallery-lite' ) );
        }

        // Get the image file path.
        $urlinfo       = parse_url( $url );
        $wp_upload_dir = wp_upload_dir();

        // Interpret the file path of the image.
        if ( preg_match( '/\/[0-9]{4}\/[0-9]{2}\/.+$/', $urlinfo['path'], $matches ) ) {
            $file_path = $wp_upload_dir['basedir'] . $matches[0];
        } else {
            $pathinfo    = parse_url( $url );
            $uploads_dir = is_multisite() ? '/files/' : '/wp-content/';
            $file_path   = ABSPATH . str_replace( dirname( $_SERVER['SCRIPT_NAME'] ) . '/', '', strstr( $pathinfo['path'], $uploads_dir ) );
            $file_path   = preg_replace( '/(\/\/)/', '/', $file_path );
        }

        // Don't process a file that does not exist.
        if ( ! file_exists( $file_path ) ) {
            return new WP_Error( 'envira-gallery-error-no-file', __( 'No file could be found for the image URL specified.', 'envira-gallery-lite' ) );
        }

        // Get original image size
        $size = @getimagesize( $file_path );

        // If no size data obtained, return an error.
        if ( ! $size ) {
            return new WP_Error( 'envira-gallery-error-no-size', __( 'The dimensions of the original image could not be retrieved for cropping.', 'envira-gallery-lite' ) );
        }

        // Set original width and height.
        list( $orig_width, $orig_height, $orig_type ) = $size;

        // Generate width or height if not provided.
        if ( $width && ! $height ) {
            $height = floor( $orig_height * ($width / $orig_width) );
        } else if ( $height && ! $width ) {
            $width = floor( $orig_width * ($height / $orig_height) );
        } else if ( ! $width && ! $height ) {
            return new WP_Error( 'envira-gallery-error-no-size', __( 'The dimensions of the original image could not be retrieved for cropping.', 'envira-gallery-lite' ) );
        }

        // Allow for different retina image sizes.
        $retina = $retina ? ( $retina === true ? 2 : $retina ) : 1;

        // Destination width and height variables
        $dest_width  = $width * $retina;
        $dest_height = $height * $retina;

        // Some additional info about the image.
        $info = pathinfo( $file_path );
        $dir  = $info['dirname'];
        $ext  = $info['extension'];
        $name = wp_basename( $file_path, ".$ext" );

        // Suffix applied to filename
        $suffix = "${dest_width}x${dest_height}";

        // Set alignment information on the file.
        if ( $crop ) {
            $suffix .= ( $align ) ? "_${align}" : "_c";
        }

        // Get the destination file name
        $dest_file_name = "${dir}/${name}-${suffix}.${ext}";

        // Return the info.
        return array(
            'dir'            => $dir,
            'name'           => $name,
            'ext'            => $ext,
            'suffix'         => $suffix,
            'orig_width'     => $orig_width,
            'orig_height'    => $orig_height,
            'orig_type'      => $orig_type,
            'dest_width'     => $dest_width,
            'dest_height'    => $dest_height,
            'file_path'      => $file_path,
            'dest_file_name' => $dest_file_name,
        );

    }

    /**
     * Helper method to flush gallery caches once a gallery is updated.
     *
     * @since 1.0.0
     *
     * @param int $post_id The current post ID.
     * @param string $slug The unique gallery slug.
     */
    public function flush_gallery_caches( $post_id, $slug = '' ) {

        // Delete known gallery caches.
        delete_transient( '_eg_cache_' . $post_id );
        delete_transient( '_eg_cache_all' );

        // Possibly delete slug gallery cache if available.
        if ( ! empty( $slug ) ) {
            delete_transient( '_eg_cache_' . $slug );
        }

        // Run a hook for Addons to access.
        do_action( 'envira_gallery_flush_caches', $post_id, $slug );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Common_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Common_Lite ) ) {
            self::$instance = new Envira_Gallery_Common_Lite();
        }

        return self::$instance;

    }

}

// Load the common class.
$envira_gallery_common_lite = Envira_Gallery_Common_Lite::get_instance();