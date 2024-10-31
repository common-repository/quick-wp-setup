<?php

/**
 *  Add menu page
 */
function qws_add_page() {
	global $qws_messages;
    $qws_hook = add_management_page( 'Quick WP Setup', // Page title
                      'Quick WP Setup', // Label in sub-menu
                      'manage_options', // capability
                      'qws-options', // page identifier 
                      'qws_do_page' ); // call back function name
                      

    //automatically runs qws.  take out later when a form is created.
	add_action( 'load-' . $qws_hook , 'qws_setup' );

}
add_action('admin_menu', 'qws_add_page');

/**
 * Enqueue the styles
 */
function qws_admin_enqueue($hook) {
    if( stripos($hook, 'qws-options' ) === FALSE)
        return;
    wp_enqueue_style( 'qws_style', plugins_url('/css/qws.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'qws_admin_enqueue' );


/**
 * Draw the menu page itself
 */
function qws_do_page() {
	global $qws_messages;
	if ( !current_user_can( 'manage_options' ) ) { 
	 wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); 
	}
	$preview_only = !isset($_POST['qws']);
	if ($preview_only) {
		$description = 'The following settings will be set up:';
	}
	else {
		$description = 'The following settings have been executed:';
	}
	?>
	<div class="wrap">
		<h2>Quick WP Setup</h2>
		<?php if (empty($qws_messages)) : ?>
		You currently do not have anything set to run for the qws_run_setup action.
		<?php else : ?>
			<h3><?php echo $description; ?></h3>
				<form method="POST">
					<input type="hidden" name="qws" value="1">
				<ol>
				<?php foreach ($qws_messages as $message) : ?>
					<li class="<?php echo $message['class'];?>"><?php echo $message['message']; ?></li>
				<?php endforeach; ?>
				</ol>
				<?php if ($preview_only) : ?>
				<input type="submit" value="Proceed">
				<?php endif;?>
			</form>
		<?php endif;?>
	</div>
	<?php	
}
