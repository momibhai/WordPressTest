<?php

function DpPm_Templates($template) {
	$custom_template = str_replace('.tpl.php', '', $template) . '-custom.tpl.php';
	$templates = array(
			$custom_template,
			$template
	);

	foreach ($templates AS $template_to_check) {
		if (is_file($template_to_check)) {
			return $template_to_check;
		} elseif (is_file(get_stylesheet_directory() . '/directorypress/public/' . $template_to_check)) { // theme or child theme templates folder
			return get_stylesheet_directory() . '/directorypress/public/' . $template_to_check;
		} elseif (is_file(DPPM_TEMPLATES_PATH . $template_to_check)) { // native plugin's templates folder
			return DPPM_TEMPLATES_PATH . $template_to_check;
		}
	}

	return false;
}

if (!function_exists('dppm_renderTemplate')) {
	function dppm_renderTemplate($template, $args = array(), $return = false) {
		global $directorypress_object;
	
		if ($args) {
			extract($args);
		}
		
		$template = apply_filters('dppm_render_template', $template, $args);
		
		if (is_array($template)) {
			$template_path = $template[0];
			$template_file = $template[1];
			$template = $template_path . $template_file;
		}
		
		$template = DpPm_Templates($template);

		if ($template) {
			if ($return) {
				ob_start();
			}
		
			include($template);
			
			if ($return) {
				$output = ob_get_contents();
				ob_end_clean();
				return $output;
			}
		}
	}
}

// package list
if( !function_exists('dppm_package_list') ){
	function dppm_package_list(){
		global $directorypress_object;             	
        $response 	= ''; 
		$response .= $directorypress_object->packages_manager->packages_list_ajax();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_package_list', 'dppm_package_list');
    add_action('wp_ajax_nopriv_dppm_package_list', 'dppm_package_list');
}

// package order
if( !function_exists('dppm_reorder') ){
	function dppm_reorder(){
		global $directorypress_object;            	
       // $response 	= array(); 
		$new_order = $_POST['new_order'];
		$action = 'reorder';
		$directorypress_object->packages_manager->packages_list_order($new_order, $action);
		$response = directorypress_renderMessages();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_reorder', 'dppm_reorder');
    add_action('wp_ajax_nopriv_dppm_reorder', 'dppm_reorder');
}
// create new package action
if( !function_exists('dppm_create_new_action') ){
	function dppm_create_new_action(){
		global $directorypress_object;             	
        $response 	= ''; 
		$response .= $directorypress_object->packages_manager->add_or_edit_package();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_create_new_action', 'dppm_create_new_action');
    add_action('wp_ajax_nopriv_dppm_create_new_action', 'dppm_create_new_action');
}
// create new package
if( !function_exists('dppm_create_new') ){
	function dppm_create_new(){
		global $directorypress_object;              	
        $response 	= array(); 
		$do_check = check_ajax_referer('directorypress_locations_depths_nonce', 'directorypress_locations_depths_nonce', false);
		if ($do_check == false) {
           $response = esc_html__('No kiddies please!', 'directorypress-extended-locations');        
        }
		$id = '';
		$action = 'submit';
		$directorypress_object->packages_manager->add_or_edit_package($id, $action);
		$response = directorypress_renderMessages();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_create_new', 'dppm_create_new');
    add_action('wp_ajax_nopriv_dppm_create_new', 'dppm_create_new');
}

// package delete action
if( !function_exists('dppm_delete_action') ){
	function dppm_delete_action(){
		global $directorypress_object;             	
        $response 	= ''; 
		$id = $_POST['id'];
		$action = '';
		$response .= '<input type="hidden" name="id" value="'.$id.'" />';
		$response .= $directorypress_object->packages_manager->delete_package($id, $action);
		
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_delete_action', 'dppm_delete_action');
    add_action('wp_ajax_nopriv_dppm_delete_action', 'dppm_delete_action');
}

// package delete
if( !function_exists('dppm_delete') ){
	function dppm_delete(){
		global $directorypress_object;            	
       // $response 	= array(); 
		$id = $_POST['id'];
		$action = 'delete';
		$directorypress_object->packages_manager->delete_package($id, $action);
		$response = directorypress_renderMessages();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_delete', 'dppm_delete');
    add_action('wp_ajax_nopriv_dppm_delete', 'dppm_delete');
}

// package edit action
if( !function_exists('dppm_edit_action') ){
	function dppm_edit_action(){
		global $directorypress_object;             	
        $response 	= ''; 
		$id = $_POST['id'];
		$action = '';
		$response .= $directorypress_object->packages_manager->add_or_edit_package($id, $action);
		
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_edit_action', 'dppm_edit_action');
    add_action('wp_ajax_nopriv_dppm_edit_action', 'dppm_edit_action');
}

// package edit
if( !function_exists('dppm_edit') ){
	function dppm_edit(){
		global $directorypress_object;            	
        $response 	= array(); 
		$do_check = check_ajax_referer('directorypress_locations_depths_nonce', 'directorypress_locations_depths_nonce', false);
		if ($do_check == false) {
           $response = esc_html__('No kiddies please!', 'directorypress-extended-locations');        
        }
		$id = $_POST['id'];
		$action = 'submit';
		$directorypress_object->packages_manager->add_or_edit_package($id, $action);
		$response = directorypress_renderMessages();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_edit', 'dppm_edit');
    add_action('wp_ajax_nopriv_dppm_edit', 'dppm_edit');
}

// package upgrade/downgrade action
if( !function_exists('dppm_upgrade_downgrade_action') ){
	function dppm_upgrade_downgrade_action(){
		global $directorypress_object;             	
        $response 	= ''; 
		$id = $_POST['id'];
		$action = '';
		$response .= $directorypress_object->packages_manager->package_upgrade_downgrade($id, $action);
		
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_upgrade_downgrade_action', 'dppm_upgrade_downgrade_action');
    add_action('wp_ajax_nopriv_dppm_upgrade_downgrade_action', 'dppm_upgrade_downgrade_action');
}

// package upgrade/downgrade
if( !function_exists('dppm_upgrade_downgrade') ){
	function dppm_upgrade_downgrade(){
		global $directorypress_object;            	
        $response 	= array(); 
		$do_check = check_ajax_referer('directorypress_packages_nonce', 'directorypress_packages_nonce', false);
		if ($do_check == false) {
           $response = esc_html__('No kiddies please!', 'directorypress-extended-locations');        
        }
		$id = $_POST['id'];
		$action = 'upgrade_downgrade';
		$directorypress_object->packages_manager->package_upgrade_downgrade($id, $action);
		//$directorypress_object->listing_single_product->packages_upgrade_meta($action);
		$response = directorypress_renderMessages();
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_upgrade_downgrade', 'dppm_upgrade_downgrade');
    add_action('wp_ajax_nopriv_dppm_upgrade_downgrade', 'dppm_upgrade_downgrade');
}

// package configuration
if( !function_exists('dppm_configure') ){
	function dppm_configure(){
		global $directorypress_object;             	
        $response 	= ''; 
		$id = $_POST['id'];		
		$response .= $directorypress_object->packages_manager->configure($id);
		echo $response; 
		die();
		
	}
	add_action('wp_ajax_dppm_configure', 'dppm_configure');
    add_action('wp_ajax_nopriv_dppm_configure', 'dppm_configure');
}