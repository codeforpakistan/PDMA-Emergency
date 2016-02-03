<?php

define('MEDIA', get_bloginfo('stylesheet_directory') . '/media');
define('LOG_FILE',  get_template_directory() . '../../topnotchdev/log.txt' );

/* ======================================================
*  GENERAL DEBUGGER
*  ====================================================== */

function logit( $msg ) {
  $file = fopen( LOG_FILE ,"w");
  fwrite($file,$msg);
  fclose($file);
} // end logit


/* ======================================================
*  ENQUEUE PARENT STYLES
*  ====================================================== */


add_action( 'wp_enqueue_scripts', 'tpn_enqueue_styles' );
function tpn_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
} // end tpn_enqueue_styles


/* ======================================================
*  REGISTER CUSTOM CATEGORY ICON WIDGET
*  ====================================================== */

add_action("widgets_init", "tpn_widgets_init");

function tpn_widgets_init() {
  register_widget('Tpn_Category_Icon_Widget');
} // end tpn_widgets_init


/**
* Displays custom category list with icons
*/
class Tpn_Category_Icon_Widget extends WP_Widget {
  
  function __construct() {
    $widget_options = array('classname' => 'widget widget_categories'  , 'description' => 'Displays categories with icons');
    parent::__construct('tpn_category_icon_widget', 'Categories with Icons' ,$widget_options);
  } // end constructor

  function widget($args, $instance){
     
  $categories = get_categories();
  $cat_icons = get_option('cat_icon');
  $upload_dir = wp_upload_dir()['baseurl'];
  echo "<aside class='widget widget_categories'>";
  echo "<h2 class='widget-title'>Categories</h2>";
  foreach ($categories as $category) { ?>
    <div class="cat" title="<?php echo $category->description; ?>">
      <a href="<?php echo get_category_link($category->cat_ID); ?>">
        <img src='<?php $icon = $cat_icons["cat_{$category->term_id}"]; if(isset($icon)) echo $upload_dir . $icon; ?>'>
        <p class="title"><?php echo $category->cat_name; ?></p>
       <span class="badge"><?php echo $category->count; ?></span>
      </a>
    </div>
<?php } // end foreach
echo "</aside>"; 
  } // end widget
  
} // end Tpn_Category_Icon_Widget