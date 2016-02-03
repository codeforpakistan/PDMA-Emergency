<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Slide class represting a single slide. This is extended by type specific
 * slides (eg, MetaImageSlide, MetaYoutubeSlide (pro only), etc)
 */
class MetaSlide {

    public $slide = 0;
    public $slider = 0;
    public $settings = array(); // slideshow settings


    /**
     * Constructor
     */
    public function __construct() {

        add_action( 'wp_ajax_change_slide_image', array( $this, 'ajax_change_slide_image' ) );

    }

    /**
     * Set the slide
     */
    public function set_slide( $id ) {
        $this->slide = get_post( $id );
    }


    /**
     * Set the slide (that this slide belongs to)
     */
    public function set_slider( $id ) {
        $this->slider = get_post( $id );
        $this->settings = get_post_meta( $id, 'ml-slider_settings', true );
    }


    /**
     * Return the HTML for the slide
     *
     * @return array complete array of slides
     */
    public function get_slide( $slide_id, $slider_id ) {
        $this->set_slider( $slider_id );
        $this->set_slide( $slide_id );
        return $this->get_slide_html();
    }


    /**
     * Save the slide
     */
    public function save_slide( $slide_id, $slider_id, $fields ) {
        $this->set_slider( $slider_id );
        $this->set_slide( $slide_id );
        $this->save( $fields );
    }


    /**
     * Change the slide image.
     *
     * This creates a copy of the selected (new) image and assigns the copy to our existing media file/slide.
     */
    public function ajax_change_slide_image() {

        if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'metaslider_changeslide' ) ) {
            wp_die( json_encode( array(
                    'status' => 'fail',
                    'msg' => __( "Security check failed. Refresh page and try again.", "ml-slider" )
                )
            ));
        }

        $slide_from = absint( $_POST['slide_from'] );
        $slide_to = absint( $_POST['slide_to'] );

        // find the paths for the image we want to change to

        // Absolute path
        $abs_path = get_attached_file( $slide_to );
        $abs_path_parts = pathinfo( $abs_path );
        $abs_file_directory = $abs_path_parts['dirname'];

        // Relative path
        $rel_path = get_post_meta( $slide_to, '_wp_attached_file', true );
        $rel_path_parts = pathinfo( $rel_path );
        $rel_file_directory = $rel_path_parts['dirname'];

        // old file name
        $file_name = $abs_path_parts['basename'];

        // new file name
        $dest_file_name = wp_unique_filename( $abs_file_directory, $file_name );

        // generate absolute and relative paths for the new file name
        $dest_abs_path = trailingslashit($abs_file_directory) . $dest_file_name;
        $dest_rel_path = trailingslashit($rel_file_directory) . $dest_file_name;

        // make a copy of the image
        if ( @ copy( $abs_path, $dest_abs_path ) ) {
            // update the path on our slide
            update_post_meta( $slide_from, '_wp_attached_file', $dest_rel_path );
            wp_update_attachment_metadata( $slide_from, wp_generate_attachment_metadata( $slide_from, $dest_abs_path ) );
            update_attached_file( $slide_from, $dest_rel_path );

            wp_die( json_encode( array(
                    'status' => 'success'
                )
            ));
        }

        wp_die( json_encode( array(
                'status' => 'fail',
                'msg' => __( "File copy failed. Please check upload directory permissions.", "ml-slider" )
            )
        ));
    }


    /**
     * Return the correct slide HTML based on whether we're viewing the slides in the
     * admin panel or on the front end.
     *
     * @return string slide html
     */
    public function get_slide_html() {

        $viewing_theme_editor = is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'metaslider-theme-editor';
        $viewing_preview = did_action('admin_post_metaslider_preview');
        $doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

        if ( $doing_ajax || $viewing_preview || $viewing_theme_editor ) {
            return $this->get_public_slide();
        }

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        if ( is_admin() && current_user_can( $capability ) ) {
            return $this->get_admin_slide();
        }

        return $this->get_public_slide();

    }


    /**
     * Check if a slide already exists in a slideshow
     */
    public function slide_exists_in_slideshow( $slider_id, $slide_id ) {

        return has_term( "{$slider_id}", 'ml-slider', $slide_id );

    }


    /**
     * Check if a slide has already been assigned to a slideshow
     */
    public function slide_is_unassigned_or_image_slide( $slider_id, $slide_id ) {

        $type = get_post_meta( $slide_id, 'ml-slider_type', true );

        return ! strlen( $type ) || $type == 'image';

    }


    /**
     * Build image HTML
     *
     * @param array   $attributes
     * @return string image HTML
     */
    public function build_image_tag( $attributes ) {

        $html = "<img";

        foreach ( $attributes as $att => $val ) {
            if ( strlen( $val ) ) {
                $html .= " " . $att . '="' . esc_attr( $val ) . '"';
            } else if ( $att == 'alt' ) {
                $html .= " " . $att . '=""'; // always include alt tag for HTML5 validation
            }
        }

        $html .= " />";

        return $html;

    }


    /**
     * Build image HTML
     *
     * @param array   $attributes
     * @return string image HTML
     */
    public function build_anchor_tag( $attributes, $content ) {

        $html = "<a";

        foreach ( $attributes as $att => $val ) {
            if ( strlen( $val ) ) {
                $html .= " " . $att . '="' . esc_attr( $val ) . '"';
            }
        }

        $html .= ">" . $content . "</a>";

        return $html;

    }


    /**
     * Tag the slide attachment to the slider tax category
     */
    public function tag_slide_to_slider() {

        if ( ! term_exists( $this->slider->ID, 'ml-slider' ) ) {
            // create the taxonomy term, the term is the ID of the slider itself
            wp_insert_term( $this->slider->ID, 'ml-slider' );
        }

        // get the term thats name is the same as the ID of the slider
        $term = get_term_by( 'name', $this->slider->ID, 'ml-slider' );
        // tag this slide to the taxonomy term
        wp_set_post_terms( $this->slide->ID, $term->term_id, 'ml-slider', true );

        $this->update_menu_order();

    }


    /**
     * Ouput the slide tabs
     */
    public function get_admin_slide_tabs_html() {

        return $this->get_admin_slide_tab_titles_html() . $this->get_admin_slide_tab_contents_html();

    }


    /**
     * Generate the HTML for the tabs
     */
    public function get_admin_slide_tab_titles_html() {

        $tabs = $this->get_admin_tabs();

        $return = "<ul class='tabs'>";

        foreach ( $tabs as $id => $tab ) {

            $pos = array_search( $id, array_keys( $tabs ) );

            $selected = $pos == 0 ? "class='selected'" : "";

            $return .= "<li {$selected} rel='tab-{$pos}'>{$tab['title']}</li>";

        }

        $return .= "</ul>";

        return $return;

    }

    /**
     * Generate the HTML for the delete button
     */
    public function get_delete_button_html() {

        $url = wp_nonce_url( admin_url( "admin-post.php?action=metaslider_delete_slide&slider_id={$this->slider->ID}&slide_id={$this->slide->ID}" ), "metaslider_delete_slide" );

        return "<a title='" . __("Delete slide", "ml-slider") . "' class='tipsy-tooltip-top delete-slide dashicons dashicons-trash' href='{$url}'>" . __("Delete slide", "ml-slider") . "</a>";

    }

    /**
     * Generate the HTML for the change slide image button
     */
    public function get_change_image_button_html() {

        return apply_filters("metaslider_change_image_button_html", "", $this->slide);

        //return "<a title='" . __("Change slide image", "ml-slider") . "' class='tipsy-tooltip-top change-image dashicons dashicons-edit' data-button-text='" . __("Change slide image", "ml-slider") . "' data-slide-id='{$this->slide->ID}'>" . __("Change slide image", "ml-slider") . "</a>";
    }

    /**
     * Generate the HTML for the tab content
     */
    public function get_admin_slide_tab_contents_html() {

        $tabs = $this->get_admin_tabs();

        $return = "<div class='tabs-content'>";

        foreach ( $tabs as $id => $tab ) {

            $pos = array_search( $id, array_keys( $tabs ) );

            $hidden = $pos != 0 ? "style='display: none;'" : "";

            $return .= "<div class='tab tab-{$pos}' {$hidden}>{$tab['content']}</div>";

        }

        $return .= "</div>";

        return $return;
    }


    /**
     * Ensure slides are added to the slideshow in the correct order.
     *
     * Find the highest slide menu_order in the slideshow, increment, then
     * update the new slides menu_order.
     */
    public function update_menu_order() {

        $menu_order = 0;

        // get the slide with the highest menu_order so far
        $args = array(
            'force_no_custom_order' => true,
            'orderby' => 'menu_order',
            'order' => 'DESC',
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'lang' => '', // polylang, ingore language filter
            'suppress_filters' => 1, // wpml, ignore language filter
            'posts_per_page' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => $this->slider->ID
                )
            )
        );

        $query = new WP_Query( $args );

        while ( $query->have_posts() ) {
            $query->next_post();
            $menu_order = $query->post->menu_order;
        }

        wp_reset_query();

        // increment
        $menu_order = $menu_order + 1;

        // update the slide
        wp_update_post( array(
                'ID' => $this->slide->ID,
                'menu_order' => $menu_order
            )
        );

    }


    /**
     * If the meta doesn't exist, add it
     * If the meta exists, but the value is empty, delete it
     * If the meta exists, update it
     */
    public function add_or_update_or_delete_meta( $post_id, $name, $value ) {

        $key = "ml-slider_" . $name;

        if ( $value == 'false' || $value == "" || ! $value ) {
            if ( get_post_meta( $post_id, $key ) ) {
                delete_post_meta( $post_id, $key );
            }
        } else {
            if ( get_post_meta( $post_id, $key ) ) {
                update_post_meta( $post_id, $key, $value );
            } else {
                add_post_meta( $post_id, $key, $value, true );
            }
        }

    }

    /**
     * Get the thumbnail for the slide
     */
    public function get_thumb() {

        $imageHelper = new MetaSliderImageHelper( $this->slide->ID, 150, 150, 'false' );
        return $imageHelper->get_image_url();

    }
}