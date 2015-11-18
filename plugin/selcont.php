<?php
/**
 *
 * Plugin Name:       SeLCont
 * Plugin URI:        http://www.netmode.ntua.gr/
 * Description:       SeLCont - Synchronized e-Learning Content Toolkit.
 * Version:           0.0.1
 * Author:            NETMODE
 * Author URI:        http://www.netmode.ntua.gr/
 * Text Domain:       selcont
 * Domain Path:       /languages
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_selcont() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-selcont-activator.php';
	Selcont_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_selcont() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-selcont-deactivator.php';
	Selcont_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_selcont' );
register_deactivation_hook( __FILE__, 'deactivate_selcont' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-selcont.php';

// Instantiate our class
$Selcont = Selcont::getInstance();


