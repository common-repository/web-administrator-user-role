<?php
/*
Plugin Name: Web Administrator User Role
Description: Plugin that automatically creates custom role for Web Administrators and allows to edit capacities for this role.
Version: 2.2
Author: Robert Kampas
Author URI: https://uk.linkedin.com/in/robertkampas
License: GPLv2 or later
*/

defined('ABSPATH') or die ('Plugin file cannot be accessed directly.');
include(ABSPATH . 'wp-includes/pluggable.php'); 

// Adding custom stylesheets
function iwa_plugin_stylesheets() {
	wp_enqueue_style('iwa_plugin_styles', plugin_dir_url(__FILE__).'web-administrator-user-role.css', false, false, 'all');
}
add_action('admin_enqueue_scripts', 'iwa_plugin_stylesheets');

// Performing plugin setup actions when plugin is activated
register_activation_hook(__FILE__, 'iwa_add_role_on_plugin_activation');
function iwa_add_role_on_plugin_activation() {
	add_role('web-administrator', 'Web Administrator');
	$role = get_role('web-administrator');
	
	// Default capacities
	$role->add_cap('create_users'); 
	$role->add_cap('delete_others_pages'); 
	$role->add_cap('delete_others_posts'); 
	$role->add_cap('delete_pages'); 
	$role->add_cap('delete_posts'); 
	$role->add_cap('delete_private_pages'); 
	$role->add_cap('delete_private_posts'); 
	$role->add_cap('delete_published_pages'); 
	$role->add_cap('delete_published_posts');	
	$role->add_cap('delete_users');	
	$role->add_cap('edit_others_pages');	
	$role->add_cap('edit_others_posts');	
	$role->add_cap('edit_pages');	
	$role->add_cap('edit_posts');	
	$role->add_cap('edit_private_pages');	
	$role->add_cap('edit_private_posts');	
	$role->add_cap('edit_published_pages');	
	$role->add_cap('edit_published_posts');	
	$role->add_cap('edit_theme_options');	
	$role->add_cap('edit_users');	
	$role->add_cap('list_users');	
	$role->add_cap('promote_users');		
	$role->add_cap('manage_links');	
	$role->add_cap('manage_categories');
	$role->add_cap('moderate_comments');		
	$role->add_cap('edit_comment');			
	$role->add_cap('publish_pages');	
	$role->add_cap('publish_posts');	
	$role->add_cap('read');
	$role->add_cap('read_private_pages');	
	$role->add_cap('read_private_posts');	
	$role->add_cap('remove_users');	
	$role->add_cap('unfiltered_html');	
	$role->add_cap('unfiltered_upload');	
	$role->add_cap('upload_files');		
}

// Performing actions when plugin is deactivated
register_deactivation_hook(__FILE__, 'iwa__remove_role_on_plugin_deactivation');
function iwa__remove_role_on_plugin_deactivation() {
	remove_role('web-administrator');
}

// Create "Settings" page sub-menu
function iwa_add_options_subpage_menu(){
	add_submenu_page('options-general.php', 'Web Administrator Settings', 'Web Administrator', 'manage_options', 'web-administrator-options', 'iwa_web_administrator_options_page'); 
}
add_action('admin_menu', 'iwa_add_options_subpage_menu');

// Remove administrator user role for web administrator
function iwa_exclude_role($roles) {
	if (is_user_logged_in()) {
		$user = wp_get_current_user();
		$role = (array) $user->roles;
	
		if ($role[0] === 'web-administrator') {
			unset($roles['administrator']);
    	
			return $roles;
		}
	}

	return $roles;
}
add_filter('editable_roles', 'iwa_exclude_role');

// Generate settings page with options
function iwa_web_administrator_options_page() {
	// Function to check if given item is inside multidimensional array 
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
		return false;
	}

	// Re-usable variables
	global $wp_roles;
	$roles = $wp_roles->roles;
	$role = get_role('web-administrator');
	$defaultGroups = array('Posts & Pages','Users','Themes','Plugins','Updates','Multisite Network','Other');

	$defaultCapacities = array(
		array('capacity' => 'manage_categories', 'group' => 'Posts & Pages'),
		array('capacity' => 'moderate_comments', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_comment', 'group' => 'Posts & Pages'),		
		array('capacity' => 'manage_links', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_others_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_others_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_published_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'publish_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_others_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_published_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_others_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_private_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_private_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'read_private_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_private_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_private_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'read_private_pages', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_published_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'edit_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_published_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'delete_posts', 'group' => 'Posts & Pages'),
		array('capacity' => 'publish_posts', 'group' => 'Posts & Pages'),

		array('capacity' => 'list_users', 'group' => 'Users'),
		array('capacity' => 'create_users', 'group' => 'Users'),
		array('capacity' => 'delete_users', 'group' => 'Users'),
		array('capacity' => 'edit_users', 'group' => 'Users'),
		array('capacity' => 'promote_users', 'group' => 'Users'),
		array('capacity' => 'remove_users', 'group' => 'Users'),

		array('capacity' => 'delete_themes', 'group' => 'Themes'),
		array('capacity' => 'edit_theme_options', 'group' => 'Themes'),
		array('capacity' => 'edit_themes', 'group' => 'Themes'),
		array('capacity' => 'install_themes', 'group' => 'Themes'),
		array('capacity' => 'switch_themes', 'group' => 'Themes'),
		array('capacity' => 'customize', 'group' => 'Themes'),

		array('capacity' => 'activate_plugins', 'group' => 'Plugins'),
		array('capacity' => 'delete_plugins', 'group' => 'Plugins'),
		array('capacity' => 'edit_plugins', 'group' => 'Plugins'),
		array('capacity' => 'install_plugins', 'group' => 'Plugins'),

		array('capacity' => 'update_core', 'group' => 'Updates'),
		array('capacity' => 'update_plugins', 'group' => 'Updates'),
		array('capacity' => 'update_themes', 'group' => 'Updates'),

		array('capacity' => 'setup_network', 'group' => 'Multisite Network'),
		array('capacity' => 'create_sites', 'group' => 'Multisite Network'),				
		array('capacity' => 'delete_sites', 'group' => 'Multisite Network'),		
		array('capacity' => 'manage_network', 'group' => 'Multisite Network'),
		array('capacity' => 'upgrade_network', 'group' => 'Multisite Network'),		
		array('capacity' => 'manage_sites', 'group' => 'Multisite Network'),
		array('capacity' => 'manage_network_users', 'group' => 'Multisite Network'),
		array('capacity' => 'manage_network_plugins', 'group' => 'Multisite Network'),
		array('capacity' => 'manage_network_themes', 'group' => 'Multisite Network'),
		array('capacity' => 'manage_network_options', 'group' => 'Multisite Network'),
		array('capacity' => 'upload_themes', 'group' => 'Multisite Network'),
		array('capacity' => 'upload_plugins', 'group' => 'Multisite Network'),

		array('capacity' => 'manage_options', 'group' => 'Other'),
		array('capacity' => 'export', 'group' => 'Other'),
		array('capacity' => 'import', 'group' => 'Other'),
		array('capacity' => 'edit_dashboard', 'group' => 'Other'),
		array('capacity' => 'upload_files', 'group' => 'Other'),
		array('capacity' => 'unfiltered_html', 'group' => 'Other'),
		array('capacity' => 'unfiltered_upload', 'group' => 'Other'),
		array('capacity' => 'read', 'group' => 'Other')
	);

	// Building array with Custom Capacities
	foreach ($roles as $key => $data_value) {
		foreach($data_value['capabilities'] as $capability => $value) {
			if (in_array_r($capability, $defaultCapacities) == false AND !preg_match('/level_/', $capability)) {
				$customCapacities[$capability] = $capability;
			}
		}
	}

	// Checking if roles are being updated
	if (isset($_POST['selected-capacities'])) {
		$postedCapacities = $_POST['selected-capacities'];

		// Remove default and custom capacities
		foreach ($defaultCapacities as $capacity) {
			$role->remove_cap($capacity['capacity']);
		}
		foreach ($customCapacities as $capacity) {
			$role->remove_cap($capacity);
		}		

		// Add selected capacities
		foreach ($postedCapacities as $capacity) {	
			$role->add_cap($capacity);
		}
		$role->add_cap('read');

		echo '<div class="updated notice notice-success is-dismissible"><p>Settings have been saved!</p></div>';
	}

	// Current capacities after update
	foreach ($role->capabilities as $key => $value) {
		$currentCapacities[] = $key;
	} ?>

	<div class="wrap">
		<h1>Web Administrator Settings</h1>
			<form method="post" >
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="selected-capacities">Default Capacities</label>
						</th>
						<td>
							<p class="description">These are default capacities build into Wordpress. See <a href="https://codex.wordpress.org/Roles_and_Capabilities#Additional_Admin_Capabilities" target="_blank">Roles and Capabilities</a> for reference.</p>
							<div class="selected-capacities-container">
								<?php
								foreach ($defaultGroups as $group) {
									echo '<strong>'.$group.'</strong>';
									foreach ($defaultCapacities as $capacity) {
										if ($capacity['group'] == $group) {
											if (in_array($capacity['capacity'], $currentCapacities)) {
												$isActive = 'checked="checked"';
											}
											else {
												$isActive = null;
											}

											if ($capacity['capacity'] == 'read') {
												$isDisabled = 'disabled="disabled"';
											}
											else {
												$isDisabled = null;
											}									

											echo '<label for="'.$capacity['capacity'].'"><input name="selected-capacities[]" id="'.$capacity['capacity'].'" value="'.$capacity['capacity'].'" '.$isActive.' '.$isDisabled.' type="checkbox">'.$capacity['capacity'].'</label>';
											
											$capacity['group'] == null;
										}
									}
								} ?>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="custom-capacities">Custom Capacities</label>
						</th>
						<td>
							<div class="selected-capacities-container">
								<?php							
								if (is_array($customCapacities)) {
									foreach ($customCapacities as $capacity) {							
										if (in_array($capacity, $currentCapacities)) {
											$isActive = 'checked="checked"';
										}
										else {
											$isActive = null;
										}
										echo '<label for="'.$capacity.'"><input name="selected-capacities[]" id="'.$capacity.'" value="'.$capacity.'" '.$isActive.' type="checkbox">'.$capacity.'</label>';
									}									
								}
								else {
									echo '<p>Currently there are no custom capacities.</p>';
								} ?>
							</div>
							<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" /></p>
						</td>
					</tr>
				</table>
			</form>
		</div>
	<?php
}