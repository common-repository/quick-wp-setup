<?php
/**
 * Default settings.  to remove any setting, remove the appropriate filter.
 * ie to remove deactivating.
 * remove_filter('qws_run_setup', 'qws_default_deactivate_plugin', 999);
 */

 /**
  * Removes default widgets
  * recent-posts
  * recent-comments
  * archives
  * categories
  */
 function qws_default_remove_active_widgets($preview_only) {
 	global $wp_registered_widgets;
 	if ($preview_only) {
 		qws_add_message('Active widgets will be removed (ie recent-posts, recent-comments, archives, categories)');
 		return;
 	}
	// These are the widgets grouped by sidebar
	$sidebars_widgets = wp_get_sidebars_widgets();
	$wp_get_widget_defaults = wp_get_widget_defaults();
	foreach($wp_get_widget_defaults as $sidebar => $val) {
		if ( !isset( $sidebars_widgets[$sidebar] ) || empty($sidebars_widgets[$sidebar]))
		    continue;

		foreach($sidebars_widgets[$sidebar] as $i => $val) {
			unset($sidebars_widgets[$sidebar][$i]);
		}		
	}
	wp_set_sidebars_widgets($sidebars_widgets);
	qws_add_message(array(
		'message' => 'Active widgets removed',
		'class'   => 'good'
	));
 }
 add_action('qws_run_setup', 'qws_default_remove_active_widgets');


/**
 * Remove the sample content
 */
function qws_default_remove_sample_page($preview_only) {
	if ($preview_only) {
		qws_add_message('Sample content (page and post) will be removed');
		return;
	}
	if ( wp_delete_post(1, true) !== FALSE && wp_delete_post(2, true) !== FALSE) {
    	qws_add_message(array(
    		'message' => 'Sample content removed',
    		'class'   => 'good'
    	));
	}

}
add_action('qws_run_setup', 'qws_default_remove_sample_page');


/**
 * By default, 
 *    Blank out blog description.
 *    Disallow users from registering
 *    Hide avatars
 *    Change timezone to hawaii.
 *    change start of the week.
 *    close comments
 *    set permalink structure to postname.
 *    Set front page to sample page.
 */
function qws_default_wp_settings($preview_only) {
	if ($preview_only) {
		qws_add_message('Default Wordpress settings will be geared towards a CMS instead of a blog.');
		return;
	}
	$defaults = array(
		'blogdescription'     => '',
		'users_can_register' => 0,
		'use_trackback'      => 0,
		'show_avatars'       => 0,
		'show_on_front'      => 'page',
		'timezone_string'    => 'Pacific/Honolulu',
		'start_of_week'      => 0,
		'default_comment_status' => 'closed',
		'default_ping_status'    => 'closed',
		'permalink_structure'    => '/%postname%/'
	);
	
	$settings = apply_filters('qws_wp_settings', $defaults);

    foreach ($settings as $key => $value) {
		update_option($key, $value);
    }
	qws_add_message(array(
		'message' => 'Default Wordpress settings are geared towards a CMS instead of a blog.',
		'class'   => 'good'
	));
}
add_action('qws_run_setup', 'qws_default_wp_settings');


/**
 * If any menus are registered, add a menu and set them to the registered menu.
 */
function qws_default_menus($preview_only) {
	if ($preview_only) {
		qws_add_message('Empty menus will be created and set to the theme\'s registered menus');
		return;
	}
	$nav_menu_locations = get_nav_menu_locations();
	$registered_menus = get_registered_nav_menus();

	//the theme needs to have menus
	if (empty($registered_menus))
		return;

	foreach ($registered_menus as $key => $title) {
		if (!empty($nav_menu_locations[$key]))
			continue;
	    $menu_id = wp_create_nav_menu($title);
	    $nav_menu_locations[$key] = $menu_id;
	}

	set_theme_mod('nav_menu_locations', $nav_menu_locations);
	qws_add_message(array(
		'message' => 'Empty menus created and set to the theme\'s registered menus', 
		'class'=>'good'
	));
}
add_action('qws_run_setup', 'qws_default_menus');


/**
 * Install plugins based on url paths. 
 */
function qws_default_install_plugins($preview_only) {
	if ($preview_only) {
		$html = '<p>Install the following plugins (one per line)</p>';
		$default_urls = apply_filters('qws_default_install_plugins', array(
			'http://wordpress.org/plugins/contact-form-7/',
			'http://wordpress.org/plugins/contact-form-7-honeypot/',
			'http://wordpress.org/plugins/contact-form-7-multi-step-module/',
			'http://wordpress.org/plugins/wordpress-seo/'
			));
		$html .= '<textarea id="qws_plugin_urls" cols="100" rows="6" name="qws_plugin_urls">' . implode("\n", $default_urls) . '</textarea>';

		$active_plugins = get_option( 'active_plugins', array() );

		//not sure how to get plugin slug to use with the api, so try this way to get url
		foreach ($active_plugins as &$plugin) {
			$parts = explode("/", $plugin);
			if (count($parts) > 1 ) {
				$plugin = $parts[0];
			}
			else {
				//no directory
				//not sure if this is correct
				$plugin_data = get_plugin_data(QWS_PLUGIN_DIR . $plugin, false, false);
				if (isset($plugin_data['Name'])) {
					$plugin = sanitize_title($plugin_data['Name']);
				}
			}
			$plugin = trailingslashit('http://wordpress.org/plugins/' . $plugin);
		}
		if ( !empty($active_plugins) ) {
			$html .= '<p class="qws_plugin_urls_installed">The following plugins are currently active: (copy these urls into qws on another installation to copy plugins)</p>';	
			$html .= '<textarea id="qws_plugin_urls_installed" cols="100" rows="6" readonly onClick="javascript:this.focus();this.select();">' . implode("\n", $active_plugins) . '</textarea>';
		}

		qws_add_message($html);
		return;
	}
	if ( isset($_POST['qws_plugin_urls']) ) {
		if ( empty($_POST['qws_plugin_urls']) ) {
			qws_add_message('<p>No plugins to install</p>');
			return;
		}
		//install plugins
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			qws_add_message(__('ERROR: You lack permissions to install and/or activate plugins.', 'qws'));
			return;
		}
		$urls = explode("\r\n", $_POST['qws_plugin_urls']);
		if (empty($urls))
			return;

		require_once( dirname( __FILE__ ) . '/includes/class-qwp-installer-skin.php' );

		$messages = '<p>Installed the following plugins</p><ul>';
		include_once ( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		foreach ($urls as $url) {
			
			$tmp = explode('/', untrailingslashit($url));
			$slug = end($tmp);
			$messages .= '<li class="qws-plugins">';
			
			$messages .= '<h3>' . $api->name  . '</h3>';
			$messages .= 'Installing ' . $url . '... <br>';

			$api = plugins_api( 'plugin_information', array(
				'slug' => $slug, 
				'fields' => array( 'sections' => false ) 
			) );


			if ( is_wp_error( $api ) ) {
				$messages .= '<span class="bad">' . sprintf(__('ERROR: Error fetching plugin information: %s', 'qws'), $api->get_error_message()) . '</span>';
			}

			$upgrader = new Plugin_Upgrader( new QWPS_Installer_Skin( array(
				'nonce'  => 'install-plugin_' . $slug,
				'url'     => $url,
				'api'    => $api
			) ) );

			$install_result = $upgrader->install( $api->download_link );

			$messages .= $upgrader->messages;


			if ( ! $install_result || is_wp_error( $install_result ) ) {
				$error_message = sprintf(__( 'Please ensure the file system is writeable or the <b>%s</b> plugin is not already installed', 'qws' ), $slug);

				if ( is_wp_error( $install_result ) )
					$error_message = $install_result->get_error_message();

				$messages .= '<span class="bad">' . sprintf( __( 'ERROR: Failed to install plugin: %s', 'qws' ), $error_message ) . '</span>';
			}
			else {
				$plugin = get_plugins( '/' . $slug );
				$pluginfiles = array_keys($plugin); //Assume the requested plugin is the first in the list
				$plugin_info = $slug . '/' . $pluginfiles[0];
				$activate_result = activate_plugin( $plugin_info );
				if ( is_wp_error( $activate_result ) ) {
					$messages .= $activate_result->get_error_message();
				}
				else {
					$messages .= '<spanc class="good">Plugin Activated</span>';
				}
			}

			$messages .= '</li>';
		}
		$messages .= '</ul>';

		qws_add_message($messages);
	}
}
add_action('qws_run_setup', 'qws_default_install_plugins');


/**
 * Add a page that will act as the home page with Lorem Ipsum... content.  
 * Sets as the front page.
 */
function qws_default_home_page($preview_only) {
	$home_id = get_option('page_on_front', 0);
	if ($preview_only) {
		if ( $home_id != 0 ) {
			qws_add_message('A home page is already set!  NO home page will be created or set as the front page.');
		}
		else {
			qws_add_message('A home page will be created and set as the front page.');	
		}
		return;
	}
	if ( $home_id != 0 ) {
		qws_add_message('No home page created or set.');	
		return;
	}

	// Create post object
	$home_page_args = array(
		'post_title'    => 'Home',
		'post_content'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam porttitor fermentum justo, id ultrices velit pharetra vitae. Interdum et malesuada fames ac ante ipsum primis in faucibus. In vitae nibh id justo feugiat sodales. Praesent ullamcorper dictum arcu, et fermentum urna. Donec aliquam ante sit amet eros feugiat accumsan sit amet et ante. Praesent pretium nulla et purus commodo commodo. Praesent quis tellus commodo, ultricies sem et, tristique ligula. Sed quis purus ut arcu molestie semper. Vivamus et molestie lacus, sit amet iaculis orci. Nunc et sem id arcu aliquet adipiscing. Integer vitae dolor vehicula, consectetur eros quis, faucibus tellus. Ut at arcu turpis. Etiam dignissim arcu ut ante pellentesque pharetra. Aenean suscipit mauris mollis gravida aliquam.',
		'post_status'   => 'publish',
		'post_type'     => 'page',
		'post_author'   => get_current_user_id()
	);
	if ( $home_id = wp_insert_post( apply_filters( 'qws_default_home_page_args', $home_page_args ) ) ) {
		update_option('page_on_front', $home_id);
	 	qws_add_message(array(
	 		'message' => 'A home page was created and set as the front page.',
	 		'class'   => 'good'
	 	));
	}
}
add_action('qws_run_setup', 'qws_default_home_page');


/**
 * Deactivates this plugin.
 */
function qws_default_deactivate_plugin($preview_only) {
	if ($preview_only) {
		qws_add_message('This plugin will be deactivated');
	}
	else {
		$plugin = qws_plugin_basename();
	 	deactivate_plugins($plugin);
	 	qws_add_message(array(
	 		'message' => 'This plugin has been deactivated',
	 		'class'   => 'good'
	 	));
	}
}
add_action('qws_run_setup', 'qws_default_deactivate_plugin', 999);
