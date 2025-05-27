<?php
/**
 * Plugin Name: HSS Vue Template
 * Description: A blank WordPress plugin using Vue 2 and Vuetify 2 in the admin panel.
 * Version: 1.0
 * Author: Your Name
 */

 /* TODO:
    - Minified Js file
    - error messaging 
 */

if ( ! defined( 'ABSPATH' )) {
	exit; // Exit if accessed directly
}

// Woocommerce Check - Necessary for all plugins
$wcActive = false;
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

// Top level Includes Go here:

// include_once plugin_dir_path( __FILE__ ) . 'includes/dbFunctions.php'; 
//include_once plugin_dir_path( __FILE__ ) . 'includes/commonDbFunctions.php';

// Check for multisite and regular
if ( ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) && ! is_plugin_active( 'woocommerce/woocommerce.php' )) {
	function require_woocommerce_plugin(){?>
		<div class="notice notice-error" >
			<p> Please Enable Woocommerce Plugin before using Supplier Order Wizard Plugin</p>
		</div>
		<?php
		@trigger_error('Please Enable Woocommerce Plugin before using Supplier Order Wizard Plugin.', E_USER_ERROR);
	}

	add_action('network_admin_notices', 'require_woocommerce_plugin');
	register_activation_hook(__FILE__, 'require_woocommerce_plugin');


} 
// If the template is not activated on network register the plugin
elseif ( ! is_plugin_active_for_network( 'hss-vue-template/hss-vue-template.php' ) && ! is_plugin_active( 'hss-vue-template/hss-vue-template.php' )) {
    // Create a DB origin for the plugin (not enabled by Default)
    function hss_vue_template_plugin_activated () {
		global $wpdb;
		error_log('hss_vue_template_plugin_activated');
		$prefix = $wpdb->prefix;

	}
	
	register_activation_hook( __FILE__, 'hss_vue_template_plugin_activated' );
}

else  {
    $wcActive = true;
    function hss_vue_template_plugin_deactivated () {
		error_log('hss_vue_template_plugin_deactivated');
        // Delete Table here or ask user if they want to keep data etc etc.
	}
	
	register_deactivation_hook( __FILE__, 'hss_vue_template_plugin_deactivated' );
}

if ($wcActive) {
    // Add all Non-admin includes here
	//include_once HSS_SO_PLUGIN_DIRPATH . 'includes/cogFunctions.php';
}

// This plugin only has admin functionality so if you are not logged in no need.
if (is_admin() && $wcActive) {
    // Add all your admin includes here
    /*
	include_once HSS_SO_PLUGIN_DIRPATH . 'includes/commonTools.php';
	include_once HSS_SO_PLUGIN_DIRPATH . 'includes/hssSupplierOrderMain.php';
	include_once HSS_SO_PLUGIN_DIRPATH . 'includes/ajax.php';
    */
	
	//Add notices if there are any
	add_action('admin_notices', 'hss_vue_template_admin_notices');
	function hss_vue_template_admin_notices() {
	if ($notices= get_option('hss_vue_template_deferred_admin_notices')) {
		foreach ($notices as $notice) {
			echo "<div class='notice notice-info is-dismissible'><p>$notice</p></div>";
		}
		delete_option('hss_vue_template_deferred_admin_notices');
	}
	}
	
	// add in script to ask to delete data on deactivate
	
}


add_action('admin_menu', 'hss_vue_add_admin_page');

// create the menu icon as well as on load instructions
function hss_vue_add_admin_page() {
    $menuSlug = 'hss-vue-template'; # Use if adding subpages/multiple pages later
    $hss_template_page = add_menu_page(
        'HSS Vue Template',
        'HSS Vue',
        'manage_options',
        $menuSlug,
        'hss_vue_render_admin_page'
    );

    add_action( 'load-' . $hss_template_page, 'hss_vue_enqueue_assets');

}

// Tell wordpress to include all dependencies and styles
function hss_vue_enqueue_assets() {
    // Vue 2 + Vuetify 2
    wp_enqueue_script('vue', 'https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js');
    wp_enqueue_script('vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.6.15/dist/vuetify.js');
    wp_enqueue_style('vuetify-css', 'https://cdn.jsdelivr.net/npm/vuetify@2.6.15/dist/vuetify.min.css');

    // Custom JS and CSS
    error_log(plugin_dir_url(__FILE__) . 'includes/js/vue-app.js');
    wp_enqueue_script('hss-vue-app', plugin_dir_url(__FILE__) . 'includes/js/vue-app.js', [], false, true);
    wp_enqueue_style('hss-admin-style', plugin_dir_url(__FILE__) . 'includes/css/admin-style.css');

    // Add AJAX dependency with nonce for security
    wp_localize_script('hss-vue-app', 'HSS_VUE_AJAX', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('hss_vue_template_nonce')
    ]);
}

// Write Vuetify code in PHP instead of templates
function hss_vue_render_admin_page() {
    error_log("loaded admin page");
    ?>
    <div id="vue-app">
        <v-app>
            <v-main>
            <v-container>
                <!-- Loader -->
                <v-overlay :value="loading" absolute>
                <v-progress-circular indeterminate size="64"></v-progress-circular>
                </v-overlay>

                <!-- Main Card -->
                <v-form ref="form">
                <v-card class="pa-4">
                    <v-card-title>Vuetify in WP Plugin</v-card-title>
                    <v-text-field
                    v-model="form.field1"
                    :rules="[rules.required]"
                    label="Example Field"
                    outlined
                    required
                    ></v-text-field>
                    <v-btn @click="submitForm" color="primary">Submit</v-btn>
                    <p>{{ ajaxResponse }}</p>
                </v-card>
                </v-form>
            </v-container>
            </v-main>
        </v-app>
    </div>
    <?php

}

// Ajax Handler Example sending data to our vue app
add_action('wp_ajax_hss_vue_sample_ajax', 'hss_vue_sample_ajax_handler');

function hss_vue_sample_ajax_handler() {
    // check if request was made by a valid token
    check_ajax_referer('hss_vue_template_nonce', 'security');

    // clean the data  and validate if empty
    $field1 = sanitize_text_field($_POST['field1'] ?? ''); 

    if (empty($field1)) {
        wp_send_json_error(['message' => 'Field cannot be empty.']);
    }

    // DB connectivity validation
    global $wpdb;
    $db_check = $wpdb->get_var("SELECT 1");

    if ($db_check != 1) {
        wp_send_json_error(['message' => 'Database connection failed.']);
    }

    wp_send_json_success(['message' => 'Form submitted successfully.']);
}
