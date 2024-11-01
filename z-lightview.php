<?php 
/*  
Plugin Name: z-Lightview
Plugin URI: http://www.zaikos.com/wp-plugins/z-lightview/
Version: 1.3.0
Author: <a href="http://www.zaikos.com/">Dave Zaikos</a>
Description: Enables <a href="http://www.nickstakenburg.com/projects/lightview/" title="Lightview">Lightview</a> for images in posts and pages, including the WordPress gallery. Due to licensing restrictions, Lightview must be <a href="http://www.nickstakenburg.com/projects/download/?project=lightview" target="_blank">downloaded separately</a>.
*/

/*  Copyright 2009  Dave Zaikos  (email : http://www.zaikos.com/contact/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Create class if it doesn't exist.

if ( !class_exists("zLightviewPlugin") ) {
	class zLightviewPlugin {

		// Lightview files to backup, individual files to omit, and backup location.

		var $lvfiles = array('css', 'images', 'js');
		var $backupomit = array('effects.js', 'prototype.js', 'lv-scriptaculous.js');
		var $backupdir = '/../.z-lightview-backup';

		// Define which extensions are images. Delimited with a vertical bar.

		var $image_extensions = "jpe?g|png|gif|bmp";

		function lvAdminMenus() {
			add_options_page('z-Lightview', 'z-Lightview', 'activate_plugins', basename(__FILE__), array(&$this, 'lvSettingsPage'));
		}

		// Recursive copy. Necessary for backups and restores.

		function recurse_copy($src,$dst) {
			if ( is_dir($src) ) {
			    $dir = opendir($src);
				@mkdir($dst);
			    while( false !== ( $file = readdir($dir)) ) {

					// Backup all files except the ones we bundle with z-Lightview.

			        if ( $file != '.' && $file != '..' && !in_array($file, $this->backupomit) ) {
			            if ( is_dir($src . '/' . $file) ) {
			                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
			            }
			            else {
			                copy($src . '/' . $file,$dst . '/' . $file);
			            }
			        }
			    }
			    closedir($dir);
			} else {
				copy($src,$dst);
			}
		}

		// Backup the Lightview JavaScript, CSS, and image files.

		function lvBackup() {

			// Return success immediately if configured to not backup the files.

			if ( $this->lvSettings('lv-backup') === FALSE ) {
				return TRUE;
			}

			// Attempt to backup the files.

			if ( is_writable(dirname(__FILE__) . '/..') ) {
				if ( !is_dir(dirname(__FILE__) . $this->backupdir) ) {
					mkdir(dirname(__FILE__) . $this->backupdir, 0777);

					foreach ( $this->lvfiles as $file ) {
						$this->recurse_copy(dirname(__FILE__) . '/' . $file, dirname(__FILE__) . $this->backupdir . '/' . $file);
					}

					return TRUE;
				}
			}

			return FALSE;
		}

		// Restore the Lightview JavaScript, CSS, and image files.

		function lvRestore() {

			// Return success immediately if configured to not backup the files.

			if ( $this->lvSettings('lv-backup') === FALSE ) {
				return TRUE;
			}

			// Attempt to restore the files.

			if ( is_dir(dirname(__FILE__) . $this->backupdir) ) {
				foreach ( $this->lvfiles as $file ) {
					$this->recurse_copy(dirname(__FILE__) . $this->backupdir . '/' . $file, dirname(__FILE__) . '/' . $file);
				}

				return TRUE;
			}

			return FALSE;
		}

		function lvSettingsPage() {

			// Get current settings, or set defaults if none exist.

			if ( get_option('z-lightview') ) {
				$options = unserialize(get_option('z-lightview'));
			} else {
				$options = array(
					'use-lightview'=>0,
					'lightview-css'=>0,
					'lightview-local'=>1,
					'lv-backup'=>TRUE
				);
			}

			// Process form submission.

			if ( isset($_POST['use-lightview']) ) {

				// Security check.

				if ( !wp_verify_nonce($_REQUEST['_wpnonce'], plugin_basename(__FILE__)) ) {
					wp_die('You do not have sufficient permissions to manage options for this blog.');
				}

				// Set settings.

				switch ( (int)$_POST['use-lightview'] ) {
					case 2:
						$options['use-lightview'] = 2;
						break;
					case 1:
						$options['use-lightview'] = 1;
						break;
					default:
						$options['use-lightview'] = 0;
						break;
				}

				switch ( (int)$_POST['lightview-css'] ) {
					case 2:
						$options['lightview-css'] = 2;
						break;
					case 1:
						$options['lightview-css'] = 1;
						break;
					default:
						$options['lightview-css'] = 0;
						break;
				}

				switch ( (int)$_POST['lightview-local'] ) {
					case 0:
						$options['lightview-local'] = 0;
						break;
					default:
						$options['lightview-local'] = 1;
						break;
				}

				switch ( (int)$_POST['lv-backup'] ) {
					case 0:
						$options['lv-backup'] = FALSE;
						break;
					default:
						$options['lv-backup'] = TRUE;
						break;
				}

				// Save settings

				if ( get_option('z-lightview') ) {
					update_option('z-lightview', serialize($options));
				} else {
					add_option('z-lightview', serialize($options), '', 'yes');
				}
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>Settings saved.</strong></p></div>";
			}

			// Generate the page.
?>

<div class="wrap">
	<h2>z-Lightview Settings</h2>

	<form method="post" action="">
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />
		<table class="form-table">
			<tr valign="top">
				<th scope="row">Enable Lightview for</th>
				<td>
					<select name="use-lightview" id="use-lightview">
						<option value="0"<?php echo ($options['use-lightview'] == 0) ? ' selected="selected"' : ''; ?>>Both individual images and the WordPress gallery</option>
						<option value="1"<?php echo ($options['use-lightview'] == 1) ? ' selected="selected"' : ''; ?>>Only individual images</option>
						<option value="2"<?php echo ($options['use-lightview'] == 2) ? ' selected="selected"' : ''; ?>>Only the WordPress gallery</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Gallery CSS Management</th>
				<td>
					<fieldset>
						<p>
							<input id="lightview-css-0" type="radio" name="lightview-css" value="0"<?php echo ($options['lightview-css'] == 0) ? ' checked="checked"' : ''; ?> />
							<label for="lightview-css-0">Include the full WordPress stylesheet code for each gallery insert (default for WordPress).</label>
						</p>
						<p>
							<input id="lightview-css-1" type="radio" name="lightview-css" value="1"<?php echo ($options['lightview-css'] == 1) ? ' checked="checked"' : ''; ?> />
							<label for="lightview-css-1">Use minimal code. This requires you have CSS code included in your theme's stylesheet to handle the layout of each gallery insert. Without such code the gallery may appear incorrectly, possibly appearing as a single column of pictures. The default code WordPress uses (which is a good place to start) is as follows:</label>
							<blockquote>
								<code>
								.gallery { margin: auto; }<br />
								.gallery-item { float: left; margin-top: 10px; text-align: center; width: 33%; }<br />
								.gallery img { border: 2px solid #cfcfcf; }<br />
								.gallery-caption { margin-left: 0; }
								</code>
							</blockquote>
						</p>
						<p>
							<input id="lightview-css-2" type="radio" name="lightview-css" value="2"<?php echo ($options['lightview-css'] == 2) ? ' checked="checked"' : ''; ?> />
							<label for="lightview-css-2">Do not include any CSS code. This will prevent any CSS code from being inserted into the page with each gallery. You will have to have all the code in your theme's stylesheet and the setting for number of columns will have no affect on appearance.</label>
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Local Images Only</th>
				<td>
					<fieldset>
						<p>
							<input id="lightview-local"type="checkbox" value="1" name="lightview-local"<?php echo ($options['lightview-local'] == 1) ? ' checked="checked"' : ''; ?> />
							<label for="lightview-local">When utilizing Lightview for single images (i.e. not the WordPress gallery), only enable Lightview for images that are local to this blog. Enabling this settings prevents images linked from other sites, such as Flickr, from being opened with Lightview.</label>
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Backup Lightview Files</th>
				<td>
					<fieldset>
						<p>
							<input id="lv-backup"type="checkbox" value="1" name="lv-backup"<?php echo ($options['lv-backup'] !== FALSE) ? ' checked="checked"' : ''; ?> />
							<label for="lv-backup">Backup Lightview JavaScript, CSS, and image files when deactivating plugin. Keeping this setting enabled will ensure that these files do not need to be downloaded after updating z-Lightview.</label>
						</p>
					</fieldset>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
		</p>
	</form>

	<br class="clear: both;" />
</div>
<?php
		}

		function lvSettings($type) {

			// Return specified setting, or default if none exist.

			if ( get_option('z-lightview') ) {
				$options = unserialize(get_option('z-lightview'));
			} else {
				$options = array(
					'use-lightview'=>0,
					'lightview-css'=>0,
					'lightview-local'=>1,
					'lv-backup'=>TRUE
				);
			}

			return $options[$type];
		}

		function lvHeaderScripts() {

			// Setup the locaton of this plugin.

			$plugin_location = dirname(plugin_basename(__FILE__));

			// Deregister older JavaScript libraries included with WordPress.	

			wp_deregister_script('prototype');
			wp_deregister_script('scriptaculous-root');
			wp_deregister_script('scriptaculous-effects');

			// Register the new JavaScript libraries.

			wp_register_script('prototype', plugins_url($plugin_location . '/js/prototype.js'), FALSE, '1.6.1');
			wp_register_script('scriptaculous-root', plugins_url($plugin_location . '/js/lv-scriptaculous.js'), FALSE, '1.8.2');
			wp_register_script('scriptaculous-effects', plugins_url($plugin_location . '/js/effects.js'), array('scriptaculous-root'), '1.8.2');

			// Enqueue the Lightview script (along with its dependancies).

			wp_enqueue_script('lightview', plugins_url($plugin_location . '/js/lightview.js'), array('prototype','scriptaculous-effects'), '2.5.1', FALSE);
		}

		function lvHeaderStyles() {

			// Enqueue the Lightview stylesheet.

			wp_enqueue_style('lightview', plugins_url(dirname(plugin_basename(__FILE__)) . '/css/lightview.css'), '', '2.5.1', 'screen');			
		}

		function lvHeaderMeta() {

			// Add meta tag for IE8 compatibility mode because IE is stupid.
			// The call to this function is commented out by default because for it to work the wp_head(); call needs
			// to be directly after <head> in the theme's header.php file. Without that, this function is useless.

			echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7\" />\n";
		}


		function normal_lightview($content){

			// Swap out single image links for Lightview use.

			if ( $this->lvSettings('lightview-local') == 1 ) {
				return preg_replace("/<a(.*?)href=(['\"])" . str_replace('/', '\/', get_option('home')) . "(.*?)\.(" . $this->image_extensions . ")(['\"])[ ]?(.*?)>/i", "<a$1href=$2" . get_option('home') . "$3.$4$5 $6 class=$2lightview$5>", $content);				
			} else {
				return preg_replace("/<a(.*?)href=(['\"])(.*?)\.(" . $this->image_extensions . ")(['\"])[ ]?(.*?)>/i", "<a$1href=$2$3.$4$5 $6 class=$2lightview$5>", $content);
			}
		}

		function gallery_lightview($attr) {

			// Replace WordPress's gallery with one that uses Lightview. Most of this is taken from the WordPress core. See gallery_shortcode() in wp-includes/media.php.

			global $post;

			static $instance = 0;
			$instance++;

			// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
			if ( isset( $attr['orderby'] ) ) {
				$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
				if ( !$attr['orderby'] )
					unset( $attr['orderby'] );
			}

			extract(shortcode_atts(array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post->ID,
				'itemtag'    => 'dl',
				'icontag'    => 'dt',
				'captiontag' => 'dd',
				'columns'    => 3,
				'size'       => 'thumbnail'
			), $attr));

			$id = intval($id);
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			if ( empty($attachments) )
				return '';

			if ( is_feed() ) {
				$output = "\n";
				foreach ( $attachments as $att_id => $attachment )
					$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
				return $output;
			}

			$itemtag = tag_escape($itemtag);
			$captiontag = tag_escape($captiontag);
			$columns = intval($columns);
			$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

			$selector = "gallery-{$instance}";

			switch ( $this->lvSettings('lightview-css') ) {
				case 2:
					$output = apply_filters('gallery_style', "
						<!-- z-Lightview gallery shortcode, see http://www.zaikos.com/wp-plugins/z-lightview/ -->
						<div id='$selector' class='gallery galleryid-{$id}'>");
					break;
				case 1:
					$output = apply_filters('gallery_style', "
						<style type='text/css'>
							#{$selector} .gallery-item {
								width: {$itemwidth}%;			}
						</style>
						<!-- z-Lightview gallery shortcode, see http://www.zaikos.com/wp-plugins/z-lightview/ -->
						<div id='$selector' class='gallery galleryid-{$id}'>");
					break;
				default:
						$output = apply_filters('gallery_style', "
							<style type='text/css'>
								#{$selector} {
									margin: auto;
								}
								#{$selector} .gallery-item {
									float: left;
									margin-top: 10px;
									text-align: center;
									width: {$itemwidth}%;			}
								#{$selector} img {
									border: 2px solid #cfcfcf;
								}
								#{$selector} .gallery-caption {
									margin-left: 0;
								}
							</style>
							<!-- z-Lightview gallery shortcode, see http://www.zaikos.com/wp-plugins/z-lightview/ -->
							<div id='$selector' class='gallery galleryid-{$id}'>");
					break;
			}

			$i = 0;
			foreach ( $attachments as $id => $attachment ) {
				// $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

				// This is where we inject the Lightview code. The above line is replaced below to ensure images are linked directly (and not attachment pages).

				$link = wp_get_attachment_link($id, $size, false, false);
				$link = preg_replace("/<a(.*?)href=(['\"])(.*?)\.(" . $this->image_extensions . ")(['\"])[ ]?(.*?)><img(.*?)><\/a>/i", "<a$1href=$2$3.$4$5 $6 class=$2lightview$5 rel=$2gallery-$instance$5><img$7></a>", $link);

				$output .= "<{$itemtag} class='gallery-item'>";
				$output .= "
					<{$icontag} class='gallery-icon'>
						$link
					</{$icontag}>";
				if ( $captiontag && trim($attachment->post_excerpt) ) {
					$output .= "
						<{$captiontag} class='gallery-caption'>
						" . wptexturize($attachment->post_excerpt) . "
						</{$captiontag}>";
				}
				$output .= "</{$itemtag}>";
				if ( $columns > 0 && ++$i % $columns == 0 )
					$output .= '<br style="clear: both" />';
			}

			$output .= "
					<br style='clear: both;' />
				</div>\n";

			return $output;
		}

		// Check that the Lightview files have been installed properly.

		function init_check_proper_install() {

			// Attempt to restore the Lightview files.

			$this->lvRestore();

			// Get the locaton of this plugin.

			$plugin_location = dirname(plugin_basename(__FILE__));

			// Check for the Lightview core.

			if ( !file_exists(WP_PLUGIN_DIR.'/'.$plugin_location.'/css/lightview.css') )
				$check = '<li><code>'.$plugin_location.'/css/lightview.css</code> file.</li>';
			if ( !file_exists(WP_PLUGIN_DIR.'/'.$plugin_location.'/js/lightview.js') )
				$check .= '<li><code>'.$plugin_location.'/js/lightview.js</code> file.</li>';
			if ( !file_exists(WP_PLUGIN_DIR.'/'.$plugin_location.'/images/lightview') )
				$check .= '<li><code>'.$plugin_location.'/images/lightview</code> folder.</li>';

			// If files we're missing, force the plugin to deactivate and throw a fatal error.

			if ( isset($check) ) {
				deactivate_plugins(plugin_basename(__FILE__), TRUE);
				die ('z-Lightview is missing one or more Lightview files (<a href="http://www.nickstakenburg.com/projects/download/?project=lightview" target="_blank">download Lightview</a> to complete the install):<ul>' . $check . '</ul>');
			}

			// Verify all settings are saved.

			if ( !get_option('z-lightview') ) {
				$options = array(
					'use-lightview'=>0,
					'lightview-css'=>0,
					'lightview-local'=>1,
					'lv-backup'=>TRUE
				);
				add_option('z-lightview', serialize($options), '', 'yes');
			}
		}
	}
}

// Initialize class.

if ( class_exists("zLightviewPlugin") && ($zlightview_plugin = new zLightviewPlugin()) ) {

	// Lightview would be sucky on iPhone's or iPod Touch's; so we won't include the headers for those. Also omitted if we're on an admin page.

	if ( !is_admin() && stristr($_SERVER['HTTP_USER_AGENT'], "iPhone") === FALSE && stristr($_SERVER['HTTP_USER_AGENT'], "iPod") === FALSE ) {
		// add_action('wp_head', array(&$zlightview_plugin, 'lvHeaderMeta'), 0);
		add_action('wp_print_scripts', array(&$zlightview_plugin, 'lvHeaderScripts'));
		add_action('wp_print_styles', array(&$zlightview_plugin, 'lvHeaderStyles'));
	}

	// Enable for the gallery if so configured.

	if ( $zlightview_plugin->lvSettings('use-lightview') != 1 ) {
		add_filter('post_gallery', array(&$zlightview_plugin, 'gallery_lightview'));
	}

	// Enable for individuals images if so configured.

	if ( $zlightview_plugin->lvSettings('use-lightview') != 2 ) {
		add_filter('the_content', array(&$zlightview_plugin, 'normal_lightview'));
	}

	// Add admin menus and install actions.

	add_action('admin_menu', array(&$zlightview_plugin, 'lvAdminMenus'));
	register_activation_hook(__FILE__, array(&$zlightview_plugin, 'init_check_proper_install'));
	register_deactivation_hook(__FILE__, array(&$zlightview_plugin, 'lvBackup'));
}