<?php
/**
 * Media class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Media_Lite {

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

        add_filter( 'wp_handle_upload', array( $this, 'fix_image_orientation' ) );

    }

    /**
    * Check if the EXIF orientation flag matches one of the values we're looking for
    * http://www.impulseadventure.com/photo/exif-orientation.html
    *
    * If it does, this means we need to rotate the image based on the orientation flag and then remove the flag.
    * This will ensure the image has the correct orientation, regardless of where it's displayed.
    *
    * Whilst most browsers and applications will read this flag to perform the rotation on displaying just the image, it's
    * not possible to do this in some situations e.g. displaying an image within a lightbox, or when the image is
    * within HTML markup.
    *
    * Orientation flags we're looking for:
    * 8: We need to rotate the image 90 degrees counter-clockwise
    * 3: We need to rotate the image 180 degrees
    * 6: We need to rotate the image 90 degrees clockwise (270 degrees counter-clockwise)
    *
    * @since 1.3.8.2
    *
    * @param array $file    Uploaded File
    * @return array         Uploaded File
    */
    public function fix_image_orientation( $file ) {

        // Check we have a file
        if ( ! file_exists( $file['file'] ) ) {
            return $file;
        }

        // Check we have a JPEG
        if ( $file['type'] !== 'image/jpg' && $file['type'] !== 'image/jpeg' ) {
            return $file;
        }

        // Attempt to read EXIF data from the image
        $exif_data = wp_read_image_metadata( $file['file'] );
        if ( ! $exif_data ) {
            return $file;
        }

        // Check if an orientation flag exists
        if ( ! isset( $exif_data['orientation'] ) ) {
            return $file;
        }

        // Check if the orientation flag matches one we're looking for
        $required_orientations = array( 8, 3, 6 );
        if ( ! in_array( $exif_data['orientation'], $required_orientations ) ) {
            return $file;
        }

        // If here, the orientation flag matches one we're looking for
        // Load the WordPress Image Editor class
        $image = wp_get_image_editor( $file['file'] );
        if ( is_wp_error( $image ) ) {
            // Something went wrong - abort
            return $file;
        } 

        // Store the source image EXIF and IPTC data in a variable, which we'll write 
        // back to the image once its orientation has changed
        // This is required because when we save an image, it'll lose its metadata.
        $source_size = getimagesize( $file['file'], $image_info );

        // Depending on the orientation flag, rotate the image
        switch ( $exif_data['orientation'] ) {

            /**
            * Rotate 90 degrees counter-clockwise
            */
            case 8:
                $image->rotate( 90 );
                break;

            /**
            * Rotate 180 degrees
            */
            case 3:
                $image->rotate( 180 );
                break;

            /**
            * Rotate 270 degrees counter-clockwise ($image->rotate always works counter-clockwise)
            */
            case 6:
                $image->rotate( 270 );
                break;

        }

        // Save the image, overwriting the existing image
        // This will discard the EXIF and IPTC data
        $image->save( $file['file'] );

        // Drop the EXIF orientation flag, otherwise applications will try to rotate the image
        // before display it, and we don't need that to happen as we've corrected the orientation

        // Write the EXIF and IPTC metadata to the revised image
        $result = $this->transfer_iptc_exif_to_image( $image_info, $file['file'], $exif_data['orientation'] );
        if ( ! $result ) {
            return $file;
        }
        
        // Read the image again to see if the EXIF data was preserved
        $exif_data = wp_read_image_metadata( $file['file'] );
        
        // Finally, return the data that's expected
        return $file;

    }

    /**
    * Transfers IPTC and EXIF data from a source image which contains either/both,
    * and saves it into a destination image's headers that might not have this IPTC
    * or EXIF data
    *
    * Useful for when you edit an image through PHP and need to preserve IPTC and EXIF
    * data
    *
    * @since 1.3.8.2
    *
    * @source http://php.net/iptcembed - ebashkoff at gmail dot com
    *
    * @param string $image_info             EXIF and IPTC image information from the source image, using getimagesize()
    * @param string $destination_image      Path and File of Destination Image, which needs IPTC and EXIF data
    * @param int    $original_orientation   The image's original orientation, before we changed it. 
    *                                       Used when we replace this orientation in the EXIF data
    * @return bool                          Success
    */
    private function transfer_iptc_exif_to_image( $image_info, $destination_image, $original_orientation ) {

        // Check destination exists
        if ( ! file_exists( $destination_image ) ) {
            return false;
        }

        // Get EXIF data from the image info, and create the IPTC segment
        $exif_data = ( ( is_array( $image_info ) && key_exists( 'APP1', $image_info ) ) ? $image_info['APP1'] : null );
        if ( $exif_data ) {
            // Find the image's original orientation flag, and change it to 1
            // This prevents applications and browsers re-rotating the image, when we've already performed that function
            $exif_data = str_replace( chr( dechex( $original_orientation ) ) , chr( 0x1 ), $exif_data );

            $exif_length = strlen( $exif_data ) + 2;
            if ( $exif_length > 0xFFFF ) {
                return false;
            }

            // Construct EXIF segment
            $exif_data = chr(0xFF) . chr(0xE1) . chr( ( $exif_length >> 8 ) & 0xFF) . chr( $exif_length & 0xFF ) . $exif_data;
        }

        // Get IPTC data from the source image, and create the IPTC segment
        $iptc_data = ( ( is_array( $image_info ) && key_exists( 'APP13', $image_info ) ) ? $image_info['APP13'] : null );
        if ( $iptc_data ) {
            $iptc_length = strlen( $iptc_data ) + 2;
            if ( $iptc_length > 0xFFFF ) {
                return false;
            }

            // Construct IPTC segment
            $iptc_data = chr(0xFF) . chr(0xED) . chr( ( $iptc_length >> 8) & 0xFF) . chr( $iptc_length & 0xFF ) . $iptc_data;
        }    

        // Get the contents of the destination image
        $destination_image_contents = file_get_contents( $destination_image );
        if ( ! $destination_image_contents ) {
            return false;
        }
        if ( strlen( $destination_image_contents ) == 0 ) {
            return false;
        }

        // Build the EXIF and IPTC data headers
        $destination_image_contents = substr( $destination_image_contents, 2 );
        $portion_to_add = chr(0xFF) . chr(0xD8); // Variable accumulates new & original IPTC application segments
        $exif_added = ! $exif_data;
        $iptc_added = ! $iptc_data;

        while ( ( substr( $destination_image_contents, 0, 2 ) & 0xFFF0 ) === 0xFFE0 ) {
            $segment_length = ( substr( $destination_image_contents, 2, 2 ) & 0xFFFF );
            $iptc_segment_number = ( substr( $destination_image_contents, 1, 1 ) & 0x0F );   // Last 4 bits of second byte is IPTC segment #
            if ( $segment_length <= 2 ) {
                return false;
            }
            
            $thisexistingsegment = substr( $destination_image_contents, 0, $segment_length + 2 );
            if ( ( 1 <= $iptc_segment_number) && ( ! $exif_added ) ) {
                $portion_to_add .= $exif_data;
                $exif_added = true;
                if ( 1 === $iptc_segment_number ) {
                    $thisexistingsegment = '';
                }
            }

            if ( ( 13 <= $iptc_segment_number ) && ( ! $iptc_added ) ) {
                $portion_to_add .= $iptc_data;
                $iptc_added = true;
                if ( 13 === $iptc_segment_number ) {
                    $thisexistingsegment = '';
                }
            }

            $portion_to_add .= $thisexistingsegment;
            $destination_image_contents = substr( $destination_image_contents, $segment_length + 2 );
        }

        // Write the EXIF and IPTC data to the new file
        if ( ! $exif_added ) {
            $portion_to_add .= $exif_data;
        }
        if ( ! $iptc_added ) {
            $portion_to_add .= $iptc_data;
        }

        $output_file = fopen( $destination_image, 'w' );
        if ( $output_file ) {
            return fwrite( $output_file, $portion_to_add . $destination_image_contents ); 
        }

        return false;
        
    }

    /**
     * Prepares a custom media upload form that allows multiple forms on one page.
     *
     * @since 1.0.0
     *
     * @return null Return early if the form cannot be output.
     */
    public function media_upload_form() {

        do_action( 'pre-upload-ui' );

        if ( ! $this->device_can_upload() ) {
            echo '<p>' . sprintf( __( 'The web browser on your device cannot be used to upload files. You may be able to use the <a href="%s">native app for your device</a> instead.', 'envira-gallery-lite' ), 'http://wordpress.org/mobile/' ) . '</p>';
            return;
        }

        if ( ! $this->has_upload_capacity() ) {
            do_action( 'upload_ui_over_quota' );
            return;
        }

        // Get both resize width and height for the media form.
        $width  = $this->get_resize_width();
        $height = $this->get_resize_height();

        // Output the media form.
        $this->do_media_upload_form( $width, $height );

    }

    /**
     * Outputs a custom media upload form that allows multiple forms on one page.
     *
     * @since 1.0.0
     *
     * @global bool $ie_IE    Flag for Internet Explorer.
     * @global bool $is_opera Flag for Opera.
     * @param int $width      The media resize width.
     * @param int $height     The media resize height.
     */
    public function do_media_upload_form( $width, $height ) {

        // Prepare globals and variables.
        global $is_IE, $is_opera;
        $sizes           = array( 'KB', 'MB', 'GB' );
        $max_upload_size = wp_max_upload_size();

        ?>
        <script type="text/javascript">var resize_width = <?php echo $width; ?>, resize_height = <?php echo $height; ?>;</script>
        <div id="envira-gallery-upload-error"></div>
        <div id="envira-gallery-plupload-upload-ui" class="hide-if-no-js">
            <?php do_action( 'pre-plupload-upload-ui' ); ?>
            <div id="envira-gallery-drag-drop-area">
                <div class="drag-drop-inside">
                    <p class="drag-drop-info"><?php _e( 'Drop images here', 'envira-gallery-lite' ); ?></p>
                    <p><?php _ex( 'or', 'Uploader: Drop images here - or - Select Images', 'envira-gallery-lite' ); ?></p>
                    <p class="drag-drop-buttons">
                        <input id="envira-gallery-plupload-browse-button" type="button" value="<?php esc_attr_e( 'Select Images', 'envira-gallery-lite' ); ?>" class="button" />
                    </p>
                </div>
            </div>
            <?php do_action( 'post-plupload-upload-ui' ); ?>
        </div>

        <div id="envira-gallery-html-upload-ui" class="hide-if-js">
            <?php do_action('pre-html-upload-ui'); ?>
            <p id="envira-gallery-async-upload-wrap">
                <label class="screen-reader-text" for="envira-gallery-async-upload"><?php _e( 'Upload', 'envira-gallery-lite' ); ?></label>
                <input type="file" name="async-upload" id="envira-gallery-async-upload" />
                <?php submit_button( __( 'Upload', 'envira-gallery-lite' ), 'button', 'html-upload', false ); ?>
                <a href="#" onclick="try{top.tb_remove();}catch(e){};return false;"><?php _e( 'Cancel', 'envira-gallery-lite' ); ?></a>
            </p>
            <div class="clear"></div>
            <?php do_action( 'post-html-upload-ui' ); ?>
        </div>

        <span class="max-upload-size"><?php printf( __( 'Maximum upload file size: %d%s.', 'envira-gallery-lite' ), esc_html( $this->get_upload_size_unit( $sizes ) ), esc_html( $sizes[$this->get_upload_size_unit( $sizes, 'unit' )] ) ); ?></span>
        <?php

        // Output a notice if the browser may have trouble with uploading large images.
        if ( ( $is_IE || $is_opera ) && $max_upload_size > 100 * 1024 * 1024 ) {
            echo '<span class="big-file-warning">' . __( 'Your browser has some limitations uploading large files with the multi-file uploader. Please use the browser uploader for files over 100MB.', 'envira-gallery-lite' ) . '</span>';
        }

        do_action( 'post-upload-ui' );

    }

    /**
     * Flag if the device can upload images.
     *
     * @since 1.0.0
     *
     * @return bool True if it can, false otherwise.
     */
    public function device_can_upload() {

        // Why is this method internal? It is quite useful.
        return _device_can_upload();

    }

    /**
     * Flag if the site has the capacity to receive an upload.
     *
     * @since 1.0.0
     *
     * @return bool True if it can, false otherwise.
     */
    public function has_upload_capacity() {

        return ! ( is_multisite() && ! is_upload_space_available() );

    }

    /**
     * Returns the resize width for the media form.
     *
     * @since 1.0.0
     *
     * @return int $width The resize width.
     */
    public function get_resize_width() {

        $width = absint( get_option( 'large_size_w' ) );
        if ( ! $width ) {
            $width = 1024;
        }

        return $width;

    }

    /**
     * Returns the resize height for the media form.
     *
     * @since 1.0.0
     *
     * @return int $width The resize height.
     */
    public function get_resize_height() {

        $height = absint( get_option( 'large_size_h' ) );
        if ( ! $height ) {
            $height = 1024;
        }

        return $height;

    }

    /**
     * Returns the upload unit for the media uploader.
     *
     * @since 1.0.0
     *
     * @param array $sizes Array of sizes to use for units.
     * @param string $type Type of unit to retrieve ('size' or 'unit').
     */
    public function get_upload_size_unit( $sizes, $type = 'size' ) {

        $upload_size_unit = wp_max_upload_size();
        for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
            $upload_size_unit /= 1024;
        }

        // If the upload size is 0, disable uploading, otherwise allow uploading to continue.
        if ( $u < 0 ) {
            $upload_size_unit = 0;
            $u                = 0;
        } else {
            $upload_size_unit = (int) $upload_size_unit;
        }

        return 'unit' == $type ? $u : $upload_size_unit;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Media_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Media_Lite ) ) {
            self::$instance = new Envira_Gallery_Media_Lite();
        }

        return self::$instance;

    }

}

// Load the media class.
$envira_gallery_media_lite = Envira_Gallery_Media_Lite::get_instance();