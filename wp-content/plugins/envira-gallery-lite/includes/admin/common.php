<?php
/**
 * Common admin class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Common_Admin_Lite {

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

        // Delete any gallery association on attachment deletion. Also delete any extra cropped images.
        add_action( 'delete_attachment', array( $this, 'delete_gallery_association' ) );
        add_action( 'delete_attachment', array( $this, 'delete_cropped_image' ) );

        // Ensure gallery display is correct when trashing/untrashing galleries.
        add_action( 'wp_trash_post', array( $this, 'trash_gallery' ) );
        add_action( 'untrash_post', array( $this, 'untrash_gallery' ) );

    }

    /**
     * Deletes the Envira gallery association for the image being deleted.
     *
     * @since 1.0.0
     *
     * @param int $attach_id The attachment ID being deleted.
     */
    public function delete_gallery_association( $attach_id ) {

        $has_gallery = get_post_meta( $attach_id, '_eg_has_gallery', true );

        // Only proceed if the image is attached to any Envira galleries.
        if ( ! empty( $has_gallery ) ) {
            foreach ( (array) $has_gallery as $post_id ) {
                // Remove the in_gallery association.
                $in_gallery = get_post_meta( $post_id, '_eg_in_gallery', true );
                if ( ! empty( $in_gallery ) ) {
                    if ( ( $key = array_search( $attach_id, (array) $in_gallery ) ) !== false ) {
                        unset( $in_gallery[$key] );
                    }
                }

                update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );

                // Remove the image from the gallery altogether.
                $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
                if ( ! empty( $gallery_data['gallery'] ) ) {
                    unset( $gallery_data['gallery'][$attach_id] );
                }

                // Update the post meta for the gallery.
                update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

                // Flush necessary gallery caches.
                Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id, ( ! empty( $gallery_data['config']['slug'] ) ? $gallery_data['config']['slug'] : '' ) );
            }
        }

    }

    /**
     * Removes any extra cropped images when an attachment is deleted.
     *
     * @since 1.0.0
     *
     * @param int $post_id The post ID.
     * @return null        Return early if the appropriate metadata cannot be retrieved.
     */
    public function delete_cropped_image( $post_id ) {

        // Get attachment image metadata.
        $metadata = wp_get_attachment_metadata( $post_id );

        // Return if no metadata is found.
        if ( ! $metadata ) {
            return;
        }

        // Return if we don't have the proper metadata.
        if ( ! isset( $metadata['file'] ) || ! isset( $metadata['image_meta']['resized_images'] ) ) {
            return;
        }

        // Grab the necessary info to removed the cropped images.
        $wp_upload_dir  = wp_upload_dir();
        $pathinfo       = pathinfo( $metadata['file'] );
        $resized_images = $metadata['image_meta']['resized_images'];

        // Loop through and deleted and resized/cropped images.
        foreach ( $resized_images as $dims ) {
            // Get the resized images filename and delete the image.
            $file = $wp_upload_dir['basedir'] . '/' . $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-' . $dims . '.' . $pathinfo['extension'];

            // Delete the resized image.
            if ( file_exists( $file ) ) {
                @unlink( $file );
            }
        }

    }

    /**
     * Trash a gallery when the gallery post type is trashed.
     *
     * @since 1.0.0
     *
     * @param $id   The post ID being trashed.
     * @return null Return early if no gallery is found.
     */
    public function trash_gallery( $id ) {

        $gallery = get_post( $id );

        // Flush necessary gallery caches to ensure trashed galleries are not showing.
        Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $id );

        // Return early if not an Envira gallery.
        if ( 'envira' !== $gallery->post_type ) {
            return;
        }

        // Set the gallery status to inactive.
        $gallery_data = get_post_meta( $id, '_eg_gallery_data', true );
        if ( empty( $gallery_data ) ) {
            return;
        }

        $gallery_data['status'] = 'inactive';
        update_post_meta( $id, '_eg_gallery_data', $gallery_data );

    }

    /**
     * Untrash a gallery when the gallery post type is untrashed.
     *
     * @since 1.0.0
     *
     * @param $id   The post ID being untrashed.
     * @return null Return early if no gallery is found.
     */
    public function untrash_gallery( $id ) {

        $gallery = get_post( $id );

        // Flush necessary gallery caches to ensure untrashed galleries are showing.
        Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $id );

        // Return early if not an Envira gallery.
        if ( 'envira' !== $gallery->post_type ) {
            return;
        }

        // Set the gallery status to inactive.
        $gallery_data = get_post_meta( $id, '_eg_gallery_data', true );
        if ( empty( $gallery_data ) ) {
            return;
        }

        if ( isset( $gallery_data['status'] ) ) {
            unset( $gallery_data['status'] );
        }

        update_post_meta( $id, '_eg_gallery_data', $gallery_data );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Common_Admin_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Common_Admin_Lite ) ) {
            self::$instance = new Envira_Gallery_Common_Admin_Lite();
        }

        return self::$instance;

    }

}

// Load the common admin class.
$envira_gallery_common_admin_lite = Envira_Gallery_Common_Admin_Lite::get_instance();