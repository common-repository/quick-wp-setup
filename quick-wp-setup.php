<?php
/*
Plugin Name: Quick WP Setup
Plugin URI: http://webheadcoder.com/wordpress-plugins/
Description: A one screen settings plugin for new wordpress installations.
Author: Webhead LLC.
Author URI: http://webheadcoder.com 
Version: 0.1
*/
/*  Copyright 2013 Webhead LLC

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/**************************************************
 
 How to use:

 To remove a default setting, remove the action in your theme like:
   remove_action('qws_run_setup', 'qws_default_remove_active_widgets');


 To add a setting, add an action in your theme and then write a function like:
 function my_setting($preview_only) {
 	if ($preview_only) {
 		//this is shown before you press the proceed button.
 		qws_add_message('Something settings will be changed');
 		return;
 	}
 	// code that makes changes the settings here...
 }
 add_action('qws_run_setup', 'my_setting');

**************************************************/

//wp plugin dir constant shouldn't be used in plugins.
define('QWS_PLUGIN_DIR', dirname(dirname(__FILE__)) . '/');

require_once('qws-options.php');
require_once('qws-defaults.php');

/**
 * Runs all the settings.  Deactivate after running b/c only need to run this once.
 */
 function qws_setup() {
 	global $qws_messages;
	$GLOBALS['qws_messages'] = array();
 	$preview_only = !isset($_POST['qws']);
 	do_action('qws_run_setup', $preview_only);
 }

function qws_plugin_basename() {
	return plugin_basename(__FILE__);
}

/**
 * Adds a message to the global qws_messages variable.  to be displayed on the page.
 */
function qws_add_message($args) {
	global $qws_messages;
	if (!is_array($args)) {
		$args = array('message' => $args);
	}
	$qws_messages[] = array_merge(array(
		'message' => '',
		'class'    => 'normal'
	), $args);
}









