<?php
/**
 * Plugin Name: Envira Gallery Lite
 * Plugin URI:  http://enviragallery.com
 * Description: Envira Gallery is best responsive WordPress gallery plugin. This is the lite version.
 * Author:      Thomas Griffin
 * Author URI:  http://thomasgriffinmedia.com
 * Version:     1.3.5.7
 * Text Domain: envira-gallery-lite
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Lite {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.3.5.7';

    /**
     * Unique plugin slug identifier.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $plugin_slug = 'envira-gallery-lite';

    /**
     * Plugin file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Fire a hook before the class is setup.
        do_action( 'envira_gallery_pre_init' );

        // Load the plugin textdomain.
        add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ), 11 );

        // Load the plugin.
        add_action( 'init', array( $this, 'init' ), 1 );

    }

    /**
     * Loads the plugin textdomain for translation.
     *
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain( 'envira-gallery-lite' );

    }

    /**
     * Loads the plugin into WordPress.
     *
     * @since 1.0.0
     */
    public function init() {

        // Do nothing if the full version of Envira Gallery is already active.
        if ( class_exists( 'Envira_Gallery' ) ) {
            return;
        }

        // Run hook once Envira has been initialized.
        do_action( 'envira_gallery_init' );

        // Load admin only components.
        if ( is_admin() ) {
            $this->require_admin();
        }

        // Load global components.
        $this->require_global();

    }

    /**
     * Loads all admin related files into scope.
     *
     * @since 1.0.0
     */
    public function require_admin() {

        require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/common.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/editor.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/media.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/posttype.php';
        require plugin_dir_path( __FILE__ ) . 'includes/admin/utils.php';

    }

    /**
     * Loads all global files into scope.
     *
     * @since 1.0.0
     */
    public function require_global() {

        require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';
        require plugin_dir_path( __FILE__ ) . 'includes/global/posttype.php';
        require plugin_dir_path( __FILE__ ) . 'includes/global/shortcode.php';

    }

    /**
     * Returns a gallery based on ID.
     *
     * @since 1.0.0
     *
     * @param int $id     The gallery ID used to retrieve a gallery.
     * @return array|bool Array of gallery data or false if none found.
     */
    public function get_gallery( $id ) {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        if ( false === ( $gallery = get_transient( '_eg_cache_' . $id ) ) ) {
            $gallery = $this->_get_gallery( $id );
            if ( $gallery ) {
                set_transient( '_eg_cache_' . $id, $gallery, DAY_IN_SECONDS );
            }
        }

        // Return the gallery data.
        return $gallery;

    }

    /**
     * Internal method that returns a gallery based on ID.
     *
     * @since 1.0.0
     *
     * @param int $id     The gallery ID used to retrieve a gallery.
     * @return array|bool Array of gallery data or false if none found.
     */
    public function _get_gallery( $id ) {

        $gallery = get_post_meta( $id, '_eg_gallery_data', true );
        if ( empty( $gallery ) || empty( $gallery['gallery'] ) ) {
            return false;
        } else {
            return $gallery;
        }

    }

    /**
     * Returns a gallery based on slug.
     *
     * @since 1.0.0
     *
     * @param string $slug The gallery slug used to retrieve a gallery.
     * @return array|bool  Array of gallery data or false if none found.
     */
    public function get_gallery_by_slug( $slug ) {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        if ( false === ( $gallery = get_transient( '_eg_cache_' . $slug ) ) ) {
            $gallery = $this->_get_gallery_by_slug( $slug );
            if ( $gallery ) {
                set_transient( '_eg_cache_' . $slug, $gallery, DAY_IN_SECONDS );
            }
        }

        // Return the gallery data.
        return $gallery;

    }

    /**
     * Internal method that returns a gallery based on slug.
     *
     * @since 1.0.0
     *
     * @param string $slug The gallery slug used to retrieve a gallery.
     * @return array|bool  Array of gallery data or false if none found.
     */
    public function _get_gallery_by_slug( $slug ) {

        $galleries = new WP_Query(
            array(
                'post_type'     => 'any',
                'posts_per_page'=> -1,
                'post_status'   => 'publish',
                'fields'        => 'ids',
                'meta_query'    => array(
                    array(
                        'key'     => '_eg_gallery_data',
                        'value'   => maybe_serialize( strval( $slug ) ),
                        'compare' => 'LIKE'
                    )
                )
            )
        );

        if ( ! isset( $galleries->posts) || empty( $galleries->posts ) ) {
            return false;
        }

        return get_post_meta( $galleries->posts[0], '_eg_gallery_data', true );

    }

    /**
     * Returns all galleries created on the site.
     *
     * @since 1.0.0
     *
     * @return array|bool Array of gallery data or false if none found.
     */
    public function get_galleries() {

        // Attempt to return the transient first, otherwise generate the new query to retrieve the data.
        if ( false === ( $galleries = get_transient( '_eg_cache_all' ) ) ) {
            $galleries = $this->_get_galleries();
            if ( $galleries ) {
                set_transient( '_eg_cache_all', $galleries, DAY_IN_SECONDS );
            }
        }

        // Return the gallery data.
        return $galleries;

    }

    /**
     * Internal method that returns all galleries created on the site.
     *
     * @since 1.0.0
     *
     * @return array|bool Array of gallery data or false if none found.
     */
    public function _get_galleries() {

        $galleries = new WP_Query(
            array(
                'post_type'     => 'any',
                'posts_per_page'=> -1,
                'post_status'   => 'publish',
                'fields'        => 'ids',
                'meta_query'    => array(
                    array(
                        'key' => '_eg_gallery_data',
                        'compare' => 'EXISTS',
                    )
                )
            )
        );

        if ( ! isset( $galleries->posts) || empty( $galleries->posts ) ) {
            return false;
        }

        // Now loop through all the galleries found and only use galleries that have images in them.
        $ret = array();
        foreach ( $galleries->posts as $id ) {
            $data = get_post_meta( $id, '_eg_gallery_data', true );
            if ( empty( $data['gallery'] ) ) {
                continue;
            }

            $ret[] = $data;
        }

        // Return the gallery data.
        return $ret;

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Lite ) ) {
            self::$instance = new Envira_Gallery_Lite();
        }

        return self::$instance;

    }

}

register_activation_hook( __FILE__, 'envira_gallery_lite_activation_hook' );
/**
 * Fired when the plugin is activated.
 *
 * @since 1.0.0
 *
 * @global int $wp_version      The version of WordPress for this install.
 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
 */
function envira_gallery_lite_activation_hook( $network_wide ) {

    global $wp_version;
    if ( version_compare( $wp_version, '3.8', '<' ) && ! defined( 'ENVIRA_FORCE_ACTIVATION' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( sprintf( __( 'Sorry, but your version of WordPress does not meet Envira Gallery\'s required version of <strong>3.8</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>.', 'envira-gallery-lite' ), get_admin_url() ) );
    }

}

// Load the main plugin class.
$envira_gallery_lite = Envira_Gallery_Lite::get_instance();

// Conditionally load the template tag.
if ( ! function_exists( 'envira_gallery' ) ) {
    /**
     * Primary template tag for outputting Envira galleries in templates.
     *
     * @since 1.0.0
     *
     * @param int $gallery_id The ID of the gallery to load.
     * @param string $type    The type of field to query.
     * @param array $args     Associative array of args to be passed.
     * @param bool $return    Flag to echo or return the gallery HTML.
     */
    function envira_gallery( $id, $type = 'id', $args = array(), $return = false ) {

        // If we have args, build them into a shortcode format.
        $args_string = '';
        if ( ! empty( $args ) ) {
            foreach ( (array) $args as $key => $value ) {
                $args_string .= ' ' . $key . '="' . $value . '"';
            }
        }

        // Build the shortcode.
        $shortcode = ! empty( $args_string ) ? '[envira-gallery ' . $type . '="' . $id . '"' . $args_string . ']' : '[envira-gallery ' . $type . '="' . $id . '"]';

        // Return or echo the shortcode output.
        if ( $return ) {
            return do_shortcode( $shortcode );
        } else {
            echo do_shortcode( $shortcode );
        }

    }
}