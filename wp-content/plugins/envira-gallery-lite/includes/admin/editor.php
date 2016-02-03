<?php
/**
 * Editor class.
 *
 * @since 1.0.0
 *
 * @package Envira_Gallery_Lite
 * @author  Thomas Griffin
 */
class Envira_Gallery_Editor_Lite {

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
     * Flag to determine if media modal is loaded.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $loaded = false;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = Envira_Gallery_Lite::get_instance();

        // Add a custom media button to the editor.
        add_filter( 'media_buttons_context', array( $this, 'media_button' ) );

    }

    /**
     * Adds a custom gallery insert button beside the media uploader button.
     *
     * @since 1.0.0
     *
     * @param string $buttons  The media buttons context HTML.
     * @return string $buttons Amended media buttons context HTML.
     */
    public function media_button( $buttons ) {

        // Create the media button.
        $button  = '<style type="text/css">@media only screen and (-webkit-min-device-pixel-ratio: 2),only screen and (min--moz-device-pixel-ratio: 2),only screen and (-o-min-device-pixel-ratio: 2/1),only screen and (min-device-pixel-ratio: 2),only screen and (min-resolution: 192dpi),only screen and (min-resolution: 2dppx) { #envira-media-modal-button .envira-media-icon[style] { background-image: url(' . plugins_url( 'assets/css/images/menu-icon@2x.png', $this->base->file ) . ') !important; background-size: 16px 16px !important; } }</style>';
        $button .= '<a id="envira-media-modal-button" href="#" class="button envira-gallery-choose-gallery" title="' . esc_attr__( 'Add Gallery', 'envira-gallery-lite' ) . '" style="padding-left: .4em;"><span class="envira-media-icon" style="background: transparent url(' . plugins_url( 'assets/css/images/menu-icon.png', $this->base->file ) . ') no-repeat scroll 0 0; width: 16px; height: 16px; display: inline-block; vertical-align: text-top;"></span> ' . __( 'Add Gallery', 'envira-gallery-lite' ) . '</a>';

        // Enqueue the script that will trigger the editor button.
        wp_enqueue_script( $this->base->plugin_slug . '-editor-script', plugins_url( 'assets/js/min/editor-min.js', $this->base->file ), array( 'jquery' ), $this->base->version, true );

        // Add the action to the footer to output the modal window.
        add_action( 'admin_footer', array( $this, 'gallery_selection_modal' ) );

        // Append the button.
        return $buttons . $button;

    }

    /**
     * Outputs the gallery selection modal to insert a gallery into an editor.
     *
     * @since 1.0.0
     */
    public function gallery_selection_modal() {

        echo $this->get_gallery_selection_modal();

    }

    /**
     * Returns the gallery selection modal to insert a gallery into an editor.
     *
     * @since 1.0.0
     *
     * @global object $post The current post object.
     * @return string Empty string if no galleries are found, otherwise modal UI.
     */
    public function get_gallery_selection_modal() {

        // Return early if already loaded.
        if ( $this->loaded ) {
            return '';
        }

        // Set the loaded flag to true.
        $this->loaded = true;

        global $post;
        $galleries = $this->base->get_galleries();

        ob_start();
        ?>
        <div class="envira-gallery-default-ui-wrapper" style="display: none;">
            <div class="envira-gallery-default-ui envira-gallery-image-meta">
                <div class="media-modal wp-core-ui">
                    <a class="media-modal-close" href="#"><span class="media-modal-icon"></span>
                    </a>
                    <div class="media-modal-content">
                        <div class="media-frame wp-core-ui hide-menu hide-router envira-gallery-meta-wrap">
                            <div class="media-frame-title">
                                <h1><?php _e( 'Choose Your Gallery', 'envira-gallery-lite' ); ?></h1>
                            </div>
                            <div class="media-frame-content">
                                <div class="attachments-browser">
                                    <ul class="envira-gallery-meta attachments" style="padding-left: 8px; top: 1em;">
                                        <li class="attachment" data-envira-gallery-id="<?php echo absint( $post->ID ); ?>" style="margin: 8px;">
                                            <div class="attachment-preview landscape">
                                                <div class="thumbnail" style="display: table;">
                                                    <div class="inside">
                                                        <h3 style="margin: 0;color: #7ad03a;"><?php _e( 'This Post\'s Gallery', 'envira-gallery-lite' ); ?></h3>
                                                        <code style="color: #7ad03a;">[envira-gallery id="<?php echo absint( $post->ID ); ?>"]</code>
                                                    </div>
                                                </div>
                                                <a class="check" href="#"><div class="media-modal-icon"></div></a>
                                            </div>
                                        </li>

                                        <?php foreach ( (array) $galleries as $gallery ) : if ( $post->ID == $gallery['id'] ) continue; ?>
                                        <li class="attachment" data-envira-gallery-id="<?php echo absint( $gallery['id'] ); ?>" style="margin: 8px;">
                                            <div class="attachment-preview landscape">
                                                <div class="thumbnail" style="display: table;">
                                                    <div class="inside">
                                                        <?php
                                                        if ( ! empty( $gallery['config']['title'] ) ) {
                                                            $title = $gallery['config']['title'];
                                                        } else if ( ! empty( $gallery['config']['slug'] ) ) {
                                                            $title = $gallery['config']['title'];
                                                        } else {
                                                            $title = sprintf( __( 'Gallery ID #%s', 'envira-gallery-lite' ), $gallery['id'] );
                                                        }
                                                        ?>
                                                        <h3 style="margin: 0;"><?php echo $title; ?></h3>
                                                        <code>[envira-gallery id="<?php echo absint( $gallery['id'] ); ?>"]</code>
                                                    </div>
                                                </div>
                                                <a class="check" href="#"><div class="media-modal-icon"></div></a>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <!-- end .envira-gallery-meta -->
                                    <div class="media-sidebar">
                                        <div class="envira-gallery-meta-sidebar">
                                            <h3 style="margin: 1.4em 0 1em;"><?php _e( 'Helpful Tips', 'envira-gallery-lite' ); ?></h3>
                                            <strong><?php _e( 'Choosing Your Gallery', 'envira-gallery-lite' ); ?></strong>
                                            <p style="margin: 0 0 1.5em;"><?php _e( 'To choose your gallery, simply click on one of the boxes to the left. The "Insert Gallery" button will be activated once you have selected a gallery.', 'envira-gallery-lite' ); ?></p>
                                            <strong><?php _e( 'Inserting Your Gallery', 'envira-gallery-lite' ); ?></strong>
                                            <p style="margin: 0 0 1.5em;"><?php _e( 'To insert your gallery into the editor, click on the "Insert Gallery" button below.', 'envira-gallery-lite' ); ?></p>
                                        </div>
                                        <!-- end .envira-gallery-meta-sidebar -->
                                    </div>
                                    <!-- end .media-sidebar -->
                                </div>
                                <!-- end .attachments-browser -->
                            </div>
                            <!-- end .media-frame-content -->
                            <div class="media-frame-toolbar">
                                <div class="media-toolbar">
                                    <div class="media-toolbar-secondary">
                                        <a href="#" class="envira-gallery-cancel-insertion button media-button button-large button-secondary media-button-insert" title="<?php esc_attr_e( 'Cancel Gallery Insertion', 'envira-gallery-lite' ); ?>"><?php _e( 'Cancel Gallery Insertion', 'envira-gallery-lite' ); ?></a>
                                    </div>
                                    <div class="media-toolbar-primary">
                                        <a href="#" class="envira-gallery-insert-gallery button media-button button-large button-primary media-button-insert" disabled="disabled" title="<?php esc_attr_e( 'Insert Gallery', 'envira-gallery-lite' ); ?>"><?php _e( 'Insert Gallery', 'envira-gallery-lite' ); ?></a>
                                    </div>
                                    <!-- end .media-toolbar-primary -->
                                </div>
                                <!-- end .media-toolbar -->
                            </div>
                            <!-- end .media-frame-toolbar -->
                        </div>
                        <!-- end .media-frame -->
                    </div>
                    <!-- end .media-modal-content -->
                </div>
                <!-- end .media-modal -->
                <div class="media-modal-backdrop"></div>
            </div><!-- end #envira-gallery-default-ui -->
        </div><!-- end #envira-gallery-default-ui-wrapper -->
        <?php
        return ob_get_clean();

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Envira_Gallery_Editor_Lite object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Gallery_Editor_Lite ) ) {
            self::$instance = new Envira_Gallery_Editor_Lite();
        }

        return self::$instance;

    }

}

// Load the editor class.
$envira_gallery_editor_lite = Envira_Gallery_Editor_Lite::get_instance();