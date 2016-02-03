<?php
/**
 * Posttype class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Posttype_Lite {

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

        // Build the labels for the post type.
        $labels = apply_filters( 'envira_gallery_post_type_labels',
            array(
                'name'               => __( 'Envira Gallery', 'envira-gallery-lite' ),
                'singular_name'      => __( 'Envira Gallery', 'envira-gallery-lite' ),
                'add_new'            => __( 'Add New', 'envira-gallery-lite' ),
                'add_new_item'       => __( 'Add New Envira Gallery', 'envira-gallery-lite' ),
                'edit_item'          => __( 'Edit Envira Gallery', 'envira-gallery-lite' ),
                'new_item'           => __( 'New Envira Gallery', 'envira-gallery-lite' ),
                'view_item'          => __( 'View Envira Gallery', 'envira-gallery-lite' ),
                'search_items'       => __( 'Search Envira Galleries', 'envira-gallery-lite' ),
                'not_found'          => __( 'No Envira galleries found.', 'envira-gallery-lite' ),
                'not_found_in_trash' => __( 'No Envira galleries found in trash.', 'envira-gallery-lite' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Envira Gallery', 'envira-gallery-lite' )
            )
        );

        // Build out the post type arguments.
        $args = apply_filters( 'envira_gallery_post_type_args',
            array(
                'labels'              => $labels,
                'public'              => false,
                'exclude_from_search' => false,
                'show_ui'             => true,
                'show_in_admin_bar'   => false,
                'rewrite'             => false,
                'query_var'           => false,
                'menu_position'       => apply_filters( 'envira_gallery_post_type_menu_position', 247 ),
                'menu_icon'           => plugins_url( 'assets/css/images/menu-icon-2x.png', $this->base->file ),
                'supports'            => array( 'title' )
            )
        );

        // Register the post type with WordPress.
        register_post_type( 'envira', $args );

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Posttype_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Posttype_Lite ) ) {
            self::$instance = new Envira_Gallery_Posttype_Lite();
        }

        return self::$instance;

    }

}

// Load the posttype class.
$envira_gallery_posttype_lite = Envira_Gallery_Posttype_Lite::get_instance();