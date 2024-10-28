<?php
/*
Plugin Name: AttNoCo
Plugin URI: https://www.10twebdesign.com/plugins/attnoco/
Description: Prevent spam comments and pingbacks on your image and other attachment files. AttNoCo allows you to turn off (or on) comments and/or pingbacks on all of your attachment files automatically, preventing all that annoying spam. For more information or to give money toward the plugin's development, <a href="https://www.10twebdesign.com/plugins/attnoco/">visit the plugin's website</a>.
Version: 1.2
Author: Brock Rogers
Author URI: http://www.brockrogers.com/
License: GPL2
*/

/* Copyright 2012 by 10T Web Design (email : brock@10twebdesign.com)
 *
 * This program is free software; you can distribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * A copy of the GNU General Public License, version 2, is located at
 * http://www.gnu.org/licenses/gpl-2.0.html . If not, please write to
 * the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor,
 * Boston, MA 02110-1301 USA.
 */

add_action('admin_menu','attnoco_menu');
add_action('add_attachment','attnoco_run');
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'attnoco_add_plugin_action_links' );

function attnoco_run() {
	if(get_option('attnoco-attachment-status')) {
		attnoco_process();
	}
}

function attnoco_process() {
	global $wpdb;
	$comment_status = get_option('attnoco-attachment-status');
	$pingback_status = get_option('attnoco-pingback-status');
	$table = $wpdb->prefix . 'posts';
	$sql = 'SELECT * FROM ' . $table . ' WHERE `post_type` = \'attachment\'';
	$results = $wpdb->get_results($sql);
	if($results) {
		foreach($results as $result) {
			$wpdb->update($table, array(
					'comment_status' => $comment_status,
					'ping_status' => $pingback_status
				), array(
					'ID' => $result->ID	
				)
			);
		}
	}
}

function attnoco_add_plugin_action_links( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-general.php?page=attnoco_settings">Settings</a>'
		),
		$links
	);
}

function attnoco_menu() {
	add_options_page('AttNoCo','AttNoCo','manage_options','attnoco_settings','attnoco_settings');
}

function attnoco_settings_save() {
		update_option('attnoco-attachment-status',$_POST['attachment-status']);
		update_option('attnoco-pingback-status',$_POST['pingback-status']);

	return '<p style="font-style:italic;">Settings saved.</p>';
}

function attnoco_settings() {
	?>
	<div class="wrap">
		<h2>AttNoCo</h2>
<?php
		if($_POST['attachment-status']) {
			echo attnoco_settings_save();
			attnoco_run();
		}
?>
		<p>AttNoCo will change the comment and ping status of all of the media in the library to the status you set below. In addition, any time a new media file is added to the library, the comment and ping status will be changed.</p>
		<form method="post" action="<?php echo str_replace('%7e', '~', $_SERVER['REQUEST_URI']); ?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="attachment_status">Set attachment comments to</label></th>
						<td>
							<select name="attachment-status">
								<option value="closed"<?php if(get_option('attnoco-attachment-status') == 'closed') {?> selected="selected"<?php } ?>>Closed</option>
								<option value="open"<?php if(!(get_option('attnoco-attachment-status') == 'closed')) {?> selected="selected"<?php } ?>>Open</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="pingback_status">Set attachment pingbacks to</label></th>
						<td>
							<select name="pingback-status">
								<option value="closed"<?php if(get_option('attnoco-pingback-status') == 'closed') {?> selected="selected"<?php } ?>>Closed</option>
								<option value="open"<?php if(!(get_option('attnoco-pingback-status') == 'closed')) {?> selected="selected"<?php } ?>>Open</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<p><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes" /></p>
		</form>
	</div>
	<p>If you find this plugin useful, please <a href="https://www.10twebdesign.com/plugins/attnoco/">donate a little bit of money toward its development.</a></p>
	<?php
}

?>
