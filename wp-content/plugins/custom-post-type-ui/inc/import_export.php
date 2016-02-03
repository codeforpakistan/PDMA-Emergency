<?php
/**
 * This file controls all of the content from the Import/Export page.
 */

# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Create our settings page output.
 *
 * @since 1.0.0
 *
 * @return string HTML output for the page.
 */
function cptui_importexport() {

	if ( !empty( $_GET ) ) {
		if ( !empty( $_GET['action'] ) && 'taxonomies' == $_GET['action'] ) {
			$tab = 'taxonomies';
		} elseif ( !empty( $_GET['action'] ) && 'get_code' == $_GET['action'] ) {
			$tab = 'get_code';
		} elseif ( !empty( $_GET['action'] ) && 'debuginfo' == $_GET['action'] ) {
			$tab = 'debuginfo';
		} else {
			$tab = 'post_types';
		}
	}

	if ( !empty( $_POST ) ) {
		$notice = cptui_import_types_taxes_settings( $_POST );
	}

	if ( isset( $notice ) ) {
		echo $notice;
	}
	echo '<div class="wrap">';

	# Create our tabs.
	cptui_settings_tab_menu( $page = 'importexport' );

	do_action( 'cptui_import_export_sections', $tab );

	echo '</div><!-- End .wrap -->';
}

/**
 * Display our copy-able code for registered taxonomies.
 *
 * @since 1.0.0
 * @since 1.2.0 Added $cptui_taxonomies parameter.
 * @since 1.2.0 Added $single parameter.
 *
 * @param array $cptui_taxonomies Array of taxonomies to render.
 * @param bool  $single           Whether or not we are rendering a single taxonomy.
 *
 * @return string Taxonomy registration text for use elsewhere.
 */
function cptui_get_taxonomy_code( $cptui_taxonomies = array(), $single = false ) {
	if ( !empty( $cptui_taxonomies ) ) {
		$callback = 'cptui_register_my_taxes';
		if ( $single ) {
			$key      = key( $cptui_taxonomies );
			$callback = 'cptui_register_my_taxes_' . str_replace('-', '_', $cptui_taxonomies[ $key ]['name'] );
		}
	?>
add_action( 'init', '<?php echo $callback; ?>' );
function <?php echo $callback; ?>() {
<?php
	foreach( $cptui_taxonomies as $tax ) {
		echo cptui_get_single_taxonomy_registery( $tax ) . "\n";
	} ?>
// End <?php echo $callback; ?>()
}
<?php
	} else {
		_e( 'No taxonomies to display at this time', 'custom-post-type-ui' );
	}
}

/**
 * Create output for single taxonomy to be ready for copy/paste from Get Code.
 *
 * @since 1.0.0
 *
 * @param array $taxonomy Taxonomy data to output.
 *
 * @return string Copy/paste ready "php" code.
 */
function cptui_get_single_taxonomy_registery( $taxonomy = array() ) {

	$post_types = "''";
	if ( is_array( $taxonomy['object_types'] ) ) {
		$post_types = 'array( "' . implode( '", "', $taxonomy['object_types'] ) . '" )';
	}

	$rewrite = get_disp_boolean( $taxonomy['rewrite'] );
	if ( false !== get_disp_boolean( $taxonomy['rewrite'] ) ) {
		$rewrite = disp_boolean( $taxonomy['rewrite'] );

		$rewrite_slug = ' \'slug\' => \'' . $taxonomy['name'] . '\',';
		if ( !empty( $taxonomy['rewrite_slug'] ) ) {
			$rewrite_slug = ' \'slug\' => \'' . $taxonomy['rewrite_slug'] . '\',';
		}

		$rewrite_withfront = '';
		$withfront = disp_boolean( $taxonomy['rewrite_withfront'] );
		if ( !empty( $withfront ) ) {
			$rewrite_withfront = ' \'with_front\' => ' . $withfront . ' ';
		}

		$hierarchical = ( !empty( $taxonomy['rewrite_hierarchical'] ) ) ? disp_boolean( $taxonomy['rewrite_hierarchical'] ) : '';
		$rewrite_hierarchcial = '';
		if ( !empty( $hierarchical ) ) {
			$rewrite_hierarchcial = ' \'hierarchical\' => ' . $hierarchical . ' ';
		}

		if ( !empty( $taxonomy['rewrite_slug'] ) || false !== disp_boolean( $taxonomy['rewrite_withfront'] ) ) {
			$rewrite_start = 'array(';
			$rewrite_end   = ')';

			$rewrite = $rewrite_start . $rewrite_slug . $rewrite_withfront . $rewrite_hierarchcial . $rewrite_end;
		}
	} else {
		$rewrite = disp_boolean( $taxonomy['rewrite'] );
	}

	?>

	$labels = array(
		"name" => "<?php echo $taxonomy['label']; ?>",
		"label" => "<?php echo $taxonomy['label']; ?>",
		<?php foreach( $taxonomy['labels'] as $key => $label ) {
			if ( !empty( $label ) ) {
			echo '"' . $key . '" => "' . $label . '",' . "\n\t\t";
			}
		} ?>);

	$args = array(
		"labels" => $labels,
		"hierarchical" => <?php echo $taxonomy['hierarchical']; ?>,
		"label" => "<?php echo $taxonomy['label']; ?>",
		"show_ui" => <?php echo disp_boolean( $taxonomy['show_ui'] ); ?>,
		"query_var" => <?php echo disp_boolean( $taxonomy['query_var'] );?>,
		"rewrite" => <?php echo $rewrite; ?>,
		"show_admin_column" => <?php echo $taxonomy['show_admin_column']; ?>,
	);
<?php # register_taxonomy( $taxonomy, $object_type, $args ); NEED TO DETERMINE THE $object_type ?>
	register_taxonomy( "<?php echo $taxonomy['name']; ?>", <?php echo $post_types; ?>, $args );
<?php
}

/**
 * Display our copy-able code for registered post types.
 *
 * @since 1.0.0
 * @since 1.2.0 Added $cptui_post_types parameter.
 * @since 1.2.0 Added $single parameter.
 *
 * @param array $cptui_post_types Array of post types to render.
 * @param bool  $single           Whether or not we are rendering a single post type.
 *
 * @return string Post type registration text for use elsewhere.
 */
function cptui_get_post_type_code( $cptui_post_types = array(), $single = false ) {
	# Whitespace very much matters here, thus why it's all flush against the left side
	if ( !empty( $cptui_post_types ) ) {
		$callback = 'cptui_register_my_cpts';
		if ( $single ) {
			$key = key( $cptui_post_types );
			$callback = 'cptui_register_my_cpts_' . str_replace( '-', '_', $cptui_post_types[ $key ]['name'] );
		}
	?>
add_action( 'init', '<?php echo $callback; ?>' );
function <?php echo $callback; ?>() {
<?php #space before this line reflects in textarea
	foreach( $cptui_post_types as $type ) {
	echo cptui_get_single_post_type_registery( $type ) . "\n";
	} ?>
// End of <?php echo $callback; ?>()
}
<?php
	} else {
		_e( 'No post types to display at this time', 'custom-post-type-ui' );
	}
}

/**
 * Create output for single post type to be ready for copy/paste from Get Code.
 *
 * @since 1.0.0
 *
 * @param array $post_type Post type data to output.
 *
 * @return string Copy/paste ready "php" code.
 */
function cptui_get_single_post_type_registery( $post_type = array() ) {

	/** This filter is documented in custom-post-type-ui/custom-post-type-ui.php */
	$post_type['map_meta_cap'] = apply_filters( 'cptui_map_meta_cap', 'true', $post_type['name'], $post_type );

	/** This filter is documented in custom-post-type-ui/custom-post-type-ui.php */
	$user_supports_params = apply_filters( 'cptui_user_supports_params', array(), $post_type['name'], $post_type );
	if ( is_array( $user_supports_params ) ) {
		$post_type['supports'] = array_merge( $post_type['supports'], $user_supports_params );
	}

	$yarpp = false; # Prevent notices.
	if ( ! empty( $post_type['custom_supports'] ) ) {
		$custom = explode( ',', $post_type['custom_supports'] );
		foreach ( $custom as $part ) {
			# We'll handle YARPP separately.
			if ( in_array( $part, array( 'YARPP', 'yarpp' ) ) ) {
				$yarpp = true;
				continue;
			}
			$post_type['supports'][] = $part;
		}
	}

	$rewrite_withfront = '';
	$rewrite = get_disp_boolean( $post_type['rewrite' ] );
	if ( false !== $rewrite ) {
		$rewrite = disp_boolean( $post_type['rewrite'] );

		$rewrite_slug = ' "slug" => "' . $post_type['name'] . '",';
		if ( !empty( $post_type['rewrite_slug'] ) ) {
			$rewrite_slug = ' "slug" => "' . $post_type['rewrite_slug'] . '",';
		}

		$withfront = disp_boolean( $post_type['rewrite_withfront'] );
		if ( !empty( $withfront ) ) {
			$rewrite_withfront = ' "with_front" => ' . $withfront . ' ';
		}

		if ( !empty( $post_type['rewrite_slug'] ) || !empty( $post_type['rewrite_withfront'] ) ) {
			$rewrite_start = 'array(';
			$rewrite_end   = ')';

			$rewrite = $rewrite_start . $rewrite_slug . $rewrite_withfront . $rewrite_end;
		}

	} else {
		$rewrite = disp_boolean( $post_type['rewrite'] );
	}

	$supports = '';
	# Do a little bit of php work to get these into strings.
	if ( !empty( $post_type['supports'] ) && is_array( $post_type['supports'] ) ) {
		$supports = 'array( "' . implode( '", "', $post_type['supports'] ) . '" )';
	}

	if ( in_array( 'none', $post_type['supports'] ) ) {
		$supports = 'false';
	}

	$taxonomies = '';
	if ( !empty( $post_type['taxonomies'] ) && is_array( $post_type['taxonomies'] ) ) {
		$taxonomies = 'array( "' . implode( '", "', $post_type['taxonomies'] ) . '" )';
	}

	if ( in_array( $post_type['query_var'], array( 'true', 'false', '0', '1' ) ) ) {
		$post_type['query_var'] = disp_boolean( $post_type['query_var'] );
	}
	if ( !empty( $post_type['query_var_slug'] ) ) {
		$post_type['query_var'] = '"' . $post_type['query_var_slug'] . '"';
	}

	if ( empty( $post_type['show_in_rest'] ) ) {
		$post_type['show_in_rest'] = 'false';
	}

	$post_type['description'] = addslashes( $post_type['description'] );
	?>
	$labels = array(
		"name" => "<?php echo $post_type['label']; ?>",
		"singular_name" => "<?php echo $post_type['singular_label']; ?>",
		<?php foreach( $post_type['labels'] as $key => $label ) {
			if ( !empty( $label ) ) {
				echo '"' . $key . '" => "' . $label . '",' . "\n\t\t";
			}
		} ?>);

	$args = array(
		"labels" => $labels,
		"description" => "<?php echo $post_type['description']; ?>",
		"public" => <?php echo disp_boolean( $post_type['public'] ); ?>,
		"show_ui" => <?php echo disp_boolean( $post_type['show_ui'] ); ?>,
		"show_in_rest" => <?php echo disp_boolean( $post_type['show_in_rest'] ); ?>,
		"has_archive" => <?php echo disp_boolean( $post_type['has_archive'] ); ?>,
		"show_in_menu" => <?php echo disp_boolean( $post_type['show_in_menu'] ); ?>,
		"exclude_from_search" => <?php echo disp_boolean( $post_type['exclude_from_search'] ); ?>,
		"capability_type" => "<?php echo $post_type['capability_type']; ?>",
		"map_meta_cap" => <?php echo disp_boolean( $post_type['map_meta_cap'] ); ?>,
		"hierarchical" => <?php echo disp_boolean( $post_type['hierarchical'] ); ?>,
		"rewrite" => <?php echo $rewrite; ?>,
		"query_var" => <?php echo $post_type['query_var']; ?>,
		<?php if ( !empty( $post_type['menu_position'] ) ) { ?>"menu_position" => <?php echo $post_type['menu_position']; ?>,<?php } ?><?php if ( !empty( $post_type['menu_icon'] ) ) { ?>"menu_icon" => "<?php echo $post_type['menu_icon']; ?>",<?php } ?>
		<?php if ( !empty( $supports ) ) { echo "\n\t\t" ?>"supports" => <?php echo $supports; ?>,<?php } ?>
		<?php if ( !empty( $taxonomies ) ) {  echo "\n\t\t" ?>"taxonomies" => <?php echo $taxonomies; ?>,<?php } ?>
		<?php if ( true === $yarpp ) { echo "\n\t\t" ?>"yarpp_support" => <?php echo disp_boolean( $yarpp ); ?><?php } echo "\n";?>
	);
	register_post_type( "<?php echo $post_type['name']; ?>", $args );
<?php
}

/**
 * Import the posted JSON data from a separate export.
 *
 * @since 1.0.0
 *
 * @param array $postdata $_POST data as json.
 *
 * @return mixed false on nothing to do, otherwise void.
 */
function cptui_import_types_taxes_settings( $postdata = array() ) {
	if ( !isset( $postdata['cptui_post_import'] ) && !isset( $postdata['cptui_tax_import'] ) ) {
		return false;
	}

	$success = false;

	/**
	 * Filters the post type data to import.
	 *
	 * Allows third parties to provide their own data dump and import instead of going through our UI.
	 *
	 * @since 1.2.0
	 *
	 * @param bool $value Default to no data.
	 */
	$third_party_post_type_data = apply_filters( 'cptui_third_party_post_type_import', false );

	/**
	 * Filters the taxonomy data to import.
	 *
	 * Allows third parties to provide their own data dump and import instead of going through our UI.
	 *
	 * @since 1.2.0
	 *
	 * @param bool $value Default to no data.
	 */
	$third_party_taxonomy_data  = apply_filters( 'cptui_third_party_taxonomy_import', false );

	if ( false !== $third_party_post_type_data ) {
		$postdata['cptui_post_import'] = $third_party_post_type_data;
	}

	if ( false !== $third_party_taxonomy_data ) {
		$postdata['cptui_tax_import'] = $third_party_taxonomy_data;
	}

	if ( !empty( $postdata['cptui_post_import'] ) ) {
		$cpt_data = stripslashes_deep( trim( $postdata['cptui_post_import'] ) );
		$settings = json_decode( $cpt_data, true );

		# Add support to delete settings outright, without accessing database.
		# Doing double check to protect.
		if ( is_null( $settings ) && '{""}' === $cpt_data ) {
			delete_option( 'cptui_post_types' );
			# We're technically successful in a sense. Importing nothing.
			$success = true;
		}

		if ( $settings ) {
			if ( false !== get_option( 'cptui_post_types' ) ) {
				delete_option( 'cptui_post_types' );
			}

			$success = update_option( 'cptui_post_types', $settings );
		}
		return cptui_admin_notices( 'import', __( 'Post types', 'custom-post-type-ui' ), $success );

  	} elseif ( !empty( $postdata['cptui_tax_import'] ) ) {
		$tax_data = stripslashes_deep( trim( $postdata['cptui_tax_import'] ) );
		$settings = json_decode( $tax_data, true );

		# Add support to delete settings outright, without accessing database.
		# Doing double check to protect.
		if ( is_null( $settings ) && '{""}' === $tax_data ) {
			delete_option( 'cptui_taxonomies' );
			# We're technically successful in a sense. Importing nothing.
			$success = true;
		}

		if ( $settings ) {
			if ( false !== get_option( 'cptui_taxonomies' ) ) {
				delete_option( 'cptui_taxonomies' );
			}

			$success = update_option( 'cptui_taxonomies', $settings );
		}
		return cptui_admin_notices( 'import', __( 'Taxonomies', 'custom-post-type-ui' ), $success );
  	}

	flush_rewrite_rules();

	return $success;
}

function cptui_render_posttypes_taxonomies_section() {
?>

	<p><?php _e( 'If you are wanting to migrate registered post types or taxonomies from this site to another, that will also use Custom Post Type UI, use the import and export functionality. If you are moving away from Custom Post Type UI, use the information in the "Get Code" tab.', 'custom-post-type-ui' ); ?></p>

<p><?php printf( '<strong>%s</strong>: %s',
			__( 'NOTE', 'custom-post-type-ui' ),
			__( 'This will not export the associated posts or taxonomy terms, just the settings.', 'custom-post-type-ui' )
	); ?>
</p>
<table class="form-table cptui-table">
	<?php if ( ! empty( $_GET ) && empty( $_GET['action'] ) ) { ?>
		<tr>
			<td class="outter">
				<label for="cptui_post_import"><h2><?php _e( 'Import Post Types', 'custom-post-type-ui' ); ?></h2>
				</label>

				<form method="post">
					<textarea class="cptui_post_import" placeholder="<?php esc_attr_e( 'Paste content here.', 'custom-post-type-ui' ); ?>" id="cptui_post_import" name="cptui_post_import"></textarea>

					<p class="wp-ui-highlight">
						<strong><?php _e( 'Note:', 'custom-post-type-ui' ); ?></strong> <?php _e( 'Importing will overwrite previous registered settings.', 'custom-post-type-ui' ); ?>
					</p>

					<p>
						<strong><?php _e( 'To import post types from a different WordPress site, paste the exported content from that site and click the "Import" button.', 'custom-post-type-ui' ); ?></strong>
					</p>

					<p>
						<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Import', 'custom-post-type-ui' ); ?>" />
					</p>
				</form>
			</td>
			<td class="outter">
				<label for="cptui_post_export"><h2><?php _e( 'Export Post Types', 'custom-post-type-ui' ); ?></h2>
				</label>
				<?php
				$cptui_post_types = get_option( 'cptui_post_types', array() );
				if ( ! empty( $cptui_post_types ) ) {
					$content = esc_html( json_encode( $cptui_post_types ) );
				} else {
					$content = __( 'No post types registered yet.', 'custom-post-type-ui' );
				}
				?>
				<textarea title="<?php esc_attr_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'custom-post-type-ui' ); ?>" onclick="this.focus();this.select()" readonly="readonly" class="cptui_post_import" id="cptui_post_export" name="cptui_post_export"><?php echo $content; ?></textarea>

				<p>
					<strong><?php _e( 'Use the content above to import current post types into a different WordPress site. You can also use this to simply back up your post type settings.', 'custom-post-type-ui' ); ?></strong>
				</p>
			</td>
		</tr>
	<?php } elseif ( ! empty( $_GET ) && 'taxonomies' == $_GET['action'] ) { ?>
		<tr>
			<td class="outter">
				<label for="cptui_tax_import"><h2><?php _e( 'Import Taxonomies', 'custom-post-type-ui' ); ?></h2>
				</label>

				<form method="post">
					<textarea class="cptui_tax_import" placeholder="<?php esc_attr_e( 'Paste content here.', 'custom-post-type-ui' ); ?>" id="cptui_tax_import" name="cptui_tax_import"></textarea>

					<p class="wp-ui-highlight">
						<strong><?php _e( 'Note:', 'custom-post-type-ui' ); ?></strong> <?php _e( 'Importing will overwrite previous registered settings.', 'custom-post-type-ui' ); ?>
					</p>

					<p>
						<strong><?php _e( 'To import taxonomies from a different WordPress site, paste the exported content from that site and click the "Import" button.', 'custom-post-type-ui' ); ?></strong>
					</p>

					<p>
						<input class="button button-primary" type="submit" value="<?php esc_attr_e( 'Import', 'custom-post-type-ui' ); ?>" />
					</p>
				</form>
			</td>
			<td class="outter">
				<label for="cptui_tax_export"><h2><?php _e( 'Export Taxonomies', 'custom-post-type-ui' ); ?></h2>
				</label>
				<?php
				$cptui_taxonomies = get_option( 'cptui_taxonomies', array() );
				if ( ! empty( $cptui_taxonomies ) ) {
					$content = esc_html( json_encode( $cptui_taxonomies ) );
				} else {
					$content = __( 'No taxonomies registered yet.', 'custom-post-type-ui' );
				}
				?>
				<textarea title="<?php esc_attr_e( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).', 'custom-post-type-ui' ); ?>" onclick="this.focus();this.select()" readonly="readonly" class="cptui_tax_import" id="cptui_tax_export" name="cptui_tax_export"><?php echo $content; ?></textarea>

				<p>
					<strong><?php _e( 'Use the content above to import current taxonomies into a different WordPress site. You can also use this to simply back up your taxonomy settings.', 'custom-post-type-ui' ); ?></strong>
				</p>
			</td>
		</tr>
	<?php } ?>
</table>
<?php
}

function cptui_render_getcode_section() {
?>
	<h1><?php _e( 'Get Post Type and Taxonomy Code', 'custom-post-type-ui' ); ?></h1>

		<h2><?php _e( 'All CPT UI Post Types', 'custom-post-type-ui' ); ?></h2>

		<?php $cptui_post_types = get_option( 'cptui_post_types' ); ?>
		<label for="cptui_post_type_get_code"><?php _e( 'Copy/paste the code below into your functions.php file.', 'custom-post-type-ui' ); ?></label>
		<textarea name="cptui_post_type_get_code" id="cptui_post_type_get_code" class="cptui_post_type_get_code" onclick="this.focus();this.select()" readonly="readonly"><?php cptui_get_post_type_code( $cptui_post_types ); ?></textarea>

		<?php
		if ( !empty( $cptui_post_types ) ) {
			foreach ( $cptui_post_types as $post_type ) { ?>
				<h2><?php
					$type = ( !empty( $post_type['label'] ) ) ? $post_type['label'] : $post_type['name'];
					printf( __( '%s Post Type', 'custom-post-type-ui' ), $type ); ?></h2>
				<label for="cptui_post_type_get_code_<?php echo $post_type['name']; ?>"><?php _e( 'Copy/paste the code below into your functions.php file.', 'custom-post-type-ui' ); ?></label>
				<textarea name="cptui_post_type_get_code_<?php echo $post_type['name']; ?>" id="cptui_post_type_get_code_<?php echo $post_type['name']; ?>" class="cptui_post_type_get_code" onclick="this.focus();this.select()" readonly="readonly"><?php cptui_get_post_type_code( array( $post_type ), true ); ?></textarea>
			<?php }
		} ?>

		<h2><?php _e( 'All CPT UI Taxonomies', 'custom-post-type-ui' ); ?></h2>

		<?php $cptui_taxonomies = get_option( 'cptui_taxonomies' ); ?>
		<label for="cptui_tax_get_code"><?php _e( 'Copy/paste the code below into your functions.php file.', 'custom-post-type-ui' ); ?></label>
		<textarea name="cptui_tax_get_code" id="cptui_tax_get_code" class="cptui_tax_get_code" onclick="this.focus();this.select()" readonly="readonly"><?php cptui_get_taxonomy_code( $cptui_taxonomies ); ?></textarea>

		<?php
		if ( ! empty( $cptui_taxonomies ) ) {
			foreach ( $cptui_taxonomies as $taxonomy ) { ?>
				<h2><?php
					$tax = ( ! empty( $taxonomy['label'] ) ) ? $taxonomy['label'] : $taxonomy['name'];
					printf( __( '%s Taxonomy', 'custom-post-type-ui' ), $tax ); ?></h2>
				<label for="cptui_tax_get_code_<?php echo $taxonomy['name']; ?>"><?php _e( 'Copy/paste the code below into your functions.php file.', 'custom-post-type-ui' ); ?></label>
				<textarea name="cptui_tax_get_code_<?php echo $taxonomy['name']; ?>" id="cptui_tax_get_code_<?php echo $taxonomy['name']; ?>" class="cptui_tax_get_code" onclick="this.focus();this.select()" readonly="readonly"><?php cptui_get_taxonomy_code( array( $taxonomy ), true ); ?></textarea>
			<?php }
		} ?>
	<?php
}

function cptui_render_debuginfo_section() {
	$debuginfo = new CPTUI_Debug_Info();

	echo '<form id="cptui_debug_info" method="post">';
	$debuginfo->tab_site_info();

	if ( ! empty( $_POST ) && isset( $_POST['cptui_debug_info_email'] ) ) {
		$email_args = array();
		$email_args['email'] = sanitize_text_field( $_POST['cptui_debug_info_email'] );
		$debuginfo->send_email( $email_args );
	}

	echo '<p><label for="cptui_debug_info_email">' . __( 'Please provide an email address to send debug information to: ', 'custom-post-type-ui' ) . '</label><input type="email" id="cptui_debug_info_email" name="cptui_debug_info_email" value="" /></p>';

	echo '<p><input type="submit" class="button-primary" name="cptui_send_debug_email" value="' . esc_attr( apply_filters( 'cptui_post_type_debug_email', __( 'Send debug info', 'custom-post-type-ui' ) ) ) . '" /></p>';
	echo '</form>';
}

function cptui_render_importexportsections( $tab ) {
	if ( isset( $tab ) ) {
		if ( 'post_types' == $tab || 'taxonomies' == $tab ) {
			cptui_render_posttypes_taxonomies_section();
		}

		if ( 'get_code' == $tab ) {
			cptui_render_getcode_section();
		}

		if ( 'debuginfo' == $tab ) {
			cptui_render_debuginfo_section();
		}
	}
}
add_action( 'cptui_import_export_sections', 'cptui_render_importexportsections' );
