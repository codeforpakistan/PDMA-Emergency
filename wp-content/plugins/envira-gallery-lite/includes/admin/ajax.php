<?php
/**
 * Handles all admin ajax interactions for the Envira Gallery plugin.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */

add_action( 'wp_ajax_envira_gallery_load_image', 'envira_gallery_lite_ajax_load_image' );
/**
 * Loads an image into a gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_load_image() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-load-image', 'nonce' );

    // Prepare variables.
    $id      = absint( $_POST['id'] );
    $post_id = absint( $_POST['post_id'] );

    // Set post meta to show that this image is attached to one or more Envira galleries.
    $has_gallery = get_post_meta( $id, '_eg_has_gallery', true );
    if ( empty( $has_gallery ) ) {
        $has_gallery = array();
    }

    $has_gallery[] = $post_id;
    update_post_meta( $id, '_eg_has_gallery', $has_gallery );

    // Set post meta to show that this image is attached to a gallery on this page.
    $in_gallery = get_post_meta( $post_id, '_eg_in_gallery', true );
    if ( empty( $in_gallery ) ) {
        $in_gallery = array();
    }

    $in_gallery[] = $id;
    update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );

    // Set data and order of image in gallery.
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
    if ( empty( $gallery_data ) ) {
        $gallery_data = array();
    }

    // If no gallery ID has been set, set it now.
    if ( empty( $gallery_data['id'] ) ) {
        $gallery_data['id'] = $post_id;
    }

    // Set data and update the meta information.
    $gallery_data = envira_gallery_lite_ajax_prepare_gallery_data( $gallery_data, $id );
    update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

    // Run hook before building out the item.
    do_action( 'envira_gallery_ajax_load_image', $id, $post_id );

    // Build out the individual HTML output for the gallery image that has just been uploaded.
    $html = Envira_Gallery_Metaboxes_Lite::get_instance()->get_gallery_item( $id, $gallery_data['gallery'][$id], $post_id );

    // Flush the gallery cache.
    Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id );

    echo json_encode( $html );
    die;

}

add_action( 'wp_ajax_envira_gallery_load_library', 'envira_gallery_lite_ajax_load_library' );
/**
 * Loads the Media Library images into the media modal window for selection.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_load_library() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-load-gallery', 'nonce' );

    // Prepare variables.
    $offset  = (int) $_POST['offset'];
    $search  = trim( stripslashes( $_POST['search'] ) );
    $post_id = absint( $_POST['post_id'] );
    $html    = '';

    // Build args
    $args = array(
        'post_type'     => 'attachment', 
        'post_mime_type'=> 'image', 
        'post_status'   => 'any', 
        'posts_per_page'=> 20, 
        'offset'        => $offset,
    );

    // Add search term to args if not empty
    if ( ! empty( $search ) ) {
        $args['s'] = $search;
    }

    // Grab the library contents
    $library = get_posts( $args );
    if ( $library ) {
        foreach ( (array) $library as $image ) {
            $has_gallery = get_post_meta( $image->ID, '_eg_has_gallery', true );
            $class       = $has_gallery && in_array( $post_id, (array) $has_gallery ) ? ' selected envira-gallery-in-gallery' : '';

            $html .= '<li class="attachment' . $class . '" data-attachment-id="' . absint( $image->ID ) . '">';
                $html .= '<div class="attachment-preview landscape">';
                    $html .= '<div class="thumbnail">';
                        $html .= '<div class="centered">';
                            $src = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
                            $html .= '<img src="' . esc_url( $src[0] ) . '" />';
                        $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<a class="check" href="#"><div class="media-modal-icon"></div></a>';
                $html .= '</div>';
            $html .= '</li>';
        }
    }

    echo json_encode( array( 'html' => stripslashes( $html ) ) );
    die;

}

add_action( 'wp_ajax_envira_gallery_library_search', 'envira_gallery_lite_ajax_library_search' );
/**
 * Searches the Media Library for images matching the term specified in the search.
 *
 * @since 1.0.0
 * @deprecated 1.2.6
 */
function envira_gallery_lite_ajax_library_search() {

    // Search is now performed by envira_gallery_lite_ajax_load_library()
    // Return the result of that function, so addons and custom code relying on this action
    // doesn't break
    envira_gallery_lite_ajax_load_library();
    die;

}

add_action( 'wp_ajax_envira_gallery_insert_images', 'envira_gallery_lite_ajax_insert_images' );
/**
 * Inserts one or more images from the Media Library into a gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_insert_images() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-insert-images', 'nonce' );

    // Prepare variables.
    $images  = stripslashes_deep( (array) $_POST['images'] );
    $post_id = absint( $_POST['post_id'] );

    // Grab and update any gallery data if necessary.
    $in_gallery = get_post_meta( $post_id, '_eg_in_gallery', true );
    if ( empty( $in_gallery ) ) {
        $in_gallery = array();
    }

    // Set data and order of image in gallery.
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
    if ( empty( $gallery_data ) ) {
        $gallery_data = array();
    }

    // If no gallery ID has been set, set it now.
    if ( empty( $gallery_data['id'] ) ) {
        $gallery_data['id'] = $post_id;
    }

    // Loop through the images and add them to the gallery.
    foreach ( (array) $images as $i => $id ) {
        // Update the attachment image post meta first.
        $has_gallery = get_post_meta( $id, '_eg_has_gallery', true );
        if ( empty( $has_gallery ) ) {
            $has_gallery = array();
        }

        $has_gallery[] = $post_id;
        update_post_meta( $id, '_eg_has_gallery', $has_gallery );

        // Now add the image to the gallery for this particular post.
        $in_gallery[] = $id;
        $gallery_data = envira_gallery_lite_ajax_prepare_gallery_data( $gallery_data, $id );
    }

    // Update the gallery data.
    update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );
    update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

    // Run hook before finishing.
    do_action( 'envira_gallery_ajax_insert_images', $images, $post_id );

    // Flush the gallery cache.
    Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_envira_gallery_sort_images', 'envira_gallery_lite_ajax_sort_images' );
/**
 * Sorts images based on user-dragged position in the gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_sort_images() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-sort', 'nonce' );

    // Prepare variables.
    $order        = explode( ',', $_POST['order'] );
    $post_id      = absint( $_POST['post_id'] );
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
    
    // Copy the gallery config, removing the images
    // Stops config from getting lost when sorting + not clicking Publish/Update
    $new_order = $gallery_data;
    unset( $new_order['gallery'] );
    $new_order['gallery'] = array();

    // Loop through the order and generate a new array based on order received.
    foreach ( $order as $id ) {
        $new_order['gallery'][$id] = $gallery_data['gallery'][$id];
    }

    // Update the gallery data.
    update_post_meta( $post_id, '_eg_gallery_data', $new_order );

    // Flush the gallery cache.
    Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_envira_gallery_remove_image', 'envira_gallery_lite_ajax_remove_image' );
/**
 * Removes an image from a gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_remove_image() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-remove-image', 'nonce' );

    // Prepare variables.
    $post_id      = absint( $_POST['post_id'] );
    $attach_id    = absint( $_POST['attachment_id'] );
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );
    $in_gallery   = get_post_meta( $post_id, '_eg_in_gallery', true );
    $has_gallery  = get_post_meta( $attach_id, '_eg_has_gallery', true );

    // Unset the image from the gallery, in_gallery and has_gallery checkers.
    unset( $gallery_data['gallery'][$attach_id] );

    if ( ( $key = array_search( $attach_id, (array) $in_gallery ) ) !== false ) {
        unset( $in_gallery[$key] );
    }

    if ( ( $key = array_search( $post_id, (array) $has_gallery ) ) !== false ) {
        unset( $has_gallery[$key] );
    }

    // Update the gallery data.
    update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );
    update_post_meta( $post_id, '_eg_in_gallery', $in_gallery );
    update_post_meta( $attach_id, '_eg_has_gallery', $has_gallery );

    // Run hook before finishing the reponse.
    do_action( 'envira_gallery_ajax_remove_images', $attach_id, $post_id );

    // Flush the gallery cache.
    Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_envira_gallery_save_meta', 'envira_gallery_lite_ajax_save_meta' );
/**
 * Saves the metadata for an image in a gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_save_meta() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-save-meta', 'nonce' );

    // Prepare variables.
    $post_id      = absint( $_POST['post_id'] );
    $attach_id    = absint( $_POST['attach_id'] );
    $meta         = $_POST['meta'];
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );

    if ( isset( $meta['title'] ) ) {
        $gallery_data['gallery'][$attach_id]['title'] = trim( $meta['title'] );
    }

    if ( isset( $meta['alt'] ) ) {
        $gallery_data['gallery'][$attach_id]['alt'] = trim( esc_html( $meta['alt'] ) );
    }

    if ( isset( $meta['link'] ) ) {
        $gallery_data['gallery'][$attach_id]['link'] = esc_url( $meta['link'] );
    }

    // Allow filtering of meta before saving.
    $gallery_data = apply_filters( 'envira_gallery_ajax_save_meta', $gallery_data, $meta, $attach_id, $post_id );

    // Update the gallery data.
    update_post_meta( $post_id, '_eg_gallery_data', $gallery_data );

    // Flush the gallery cache.
    Envira_Gallery_Common_Lite::get_instance()->flush_gallery_caches( $post_id );

    echo json_encode( true );
    die;

}

add_action( 'wp_ajax_envira_gallery_refresh', 'envira_gallery_lite_ajax_refresh' );
/**
 * Refreshes the DOM view for a gallery.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_refresh() {

    // Run a security check first.
    check_ajax_referer( 'envira-gallery-refresh', 'nonce' );

    // Prepare variables.
    $post_id = absint( $_POST['post_id'] );
    $gallery = '';

    // Grab all gallery data.
    $gallery_data = get_post_meta( $post_id, '_eg_gallery_data', true );

    // If there are no gallery items, don't do anything.
    if ( empty( $gallery_data ) || empty( $gallery_data['gallery'] ) ) {
        echo json_encode( array( 'error' => true ) );
        die;
    }

    // Loop through the data and build out the gallery view.
    foreach ( (array) $gallery_data['gallery'] as $id => $data ) {
        $gallery .= Envira_Gallery_Metaboxes_Lite::get_instance()->get_gallery_item( $id, $data, $post_id );
    }

    echo json_encode( array( 'success' => $gallery ) );
    die;

}

add_action( 'wp_ajax_envira_gallery_load_gallery_data', 'envira_gallery_lite_ajax_load_gallery_data' );
/**
 * Retrieves and return gallery data for the specified ID.
 *
 * @since 1.0.0
 */
function envira_gallery_lite_ajax_load_gallery_data() {

    // Prepare variables and grab the gallery data.
    $gallery_id   = absint( $_POST['post_id'] );
    $gallery_data = get_post_meta( $gallery_id, '_eg_gallery_data', true );

    // Send back the gallery data.
    echo json_encode( $gallery_data );
    die;

}

/**
 * Helper function to prepare the metadata for an image in a gallery.
 *
 * @since 1.0.0
 *
 * @param array $gallery_data  Array of data for the gallery.
 * @param int $id              The attachment ID to prepare data for.
 * @return array $gallery_data Amended gallery data with updated image metadata.
 */
function envira_gallery_lite_ajax_prepare_gallery_data( $gallery_data, $id ) {

    $attachment = get_post( $id );
    $url        = wp_get_attachment_image_src( $id, 'full' );
    $alt_text   = get_post_meta( $id, '_wp_attachment_image_alt', true );
    $gallery_data['gallery'][$id] = array(
        'status'  => 'pending',
        'src'     => isset( $url[0] ) ? esc_url( $url[0] ) : '',
        'title'   => get_the_title( $id ),
        'link'    => isset( $url[0] ) ? esc_url( $url[0] ) : '',
        'alt'     => ! empty( $alt_text ) ? $alt_text : '',
        'thumb'   => ''
    );

    $gallery_data = apply_filters( 'envira_gallery_ajax_item_data', $gallery_data, $attachment, $id );

    return $gallery_data;

}