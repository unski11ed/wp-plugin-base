<?php
/**
 * Plugin Name: __PluginName__
 * Description: __PluginDescription__
 * Version:     __PluginVersion__
 * Author:      __PluginAuthor__
 */

namespace __PluginNamespace__;

use __PluginNamespace__\Service as Service;

//=============================Initialization===================================
// Include Base
require_once plugin_dir_path(__FILE__).'backend/base/general.php';
require_once plugin_dir_path(__FILE__).'backend/base/executable.php';
require_once plugin_dir_path(__FILE__).'backend/base/controller.php';
require_once plugin_dir_path(__FILE__).'backend/base/shortcode.php';
require_once plugin_dir_path(__FILE__).'backend/base/model.php';
// Register Services
require_once plugin_dir_path(__FILE__).'backend/app/service.php';

Service\PluginMVCService::register_routes();
Service\PluginMVCService::register_shortcodes();
register_activation_hook(__FILE__, function() {
    Service\PluginMVCService::initialize_models();
});
//===============================Load scripts===================================
add_action( 'wp_enqueue_scripts', function(){
    wp_enqueue_script("user-script", plugin_dir_url(__FILE__) . "/frontend/js/main.min.js", array(), false, false);
    wp_enqueue_style('user-style', plugin_dir_url(__FILE__) . "/frontend/css/main.min.css");
});
add_action( 'admin_enqueue_scripts', function(){
    wp_enqueue_script("user-script", plugin_dir_url(__FILE__) . "/frontend/js/main.min.js", array(), false, false);
    wp_enqueue_style('user-style', plugin_dir_url(__FILE__) . "/frontend/css/main.min.css");
});

//===============================Setup admin menu===============================
add_action('admin_menu', function(){
    $menuManager = new Service\PluginMVCMenuPages("Todos");

    $menuParent = $menuManager->insert_page("Todos", "Todos", "manage", "List");
});
