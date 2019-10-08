<?php
/**
 * Plugin Name: Aplikacja Kalendarza rezerwacji
 * Description: Kalendarz do rezerwowania miejsc
 * Version:     1.0
 * Author:      Upstrakt
 * Author URI:  http://www.upstrakt.pl
 *
 * Copyright (c) 2014 Upstrakt
 */

//=============================Initialization===================================
require_once plugin_dir_path(__FILE__)."/settings.php";
require_once plugin_dir_path(__FILE__)."/Base/general.php";

//Register services
require_once plugin_dir_path(__FILE__).'/Core/service.php';

PluginMVCService::RegisterRoutes();
PluginMVCService::RegisterShortcodes();

//============================Load Php Libraries================================
require_once plugin_dir_path(__FILE__)."Core/Helper/email.php";

//===============================Load scripts===================================
add_action( 'wp_enqueue_scripts', function(){
    wp_enqueue_script("jquery.numeric", plugin_dir_url(__FILE__) . "/js/lib/jquery.numeric.min.js", array('jquery'));
    wp_enqueue_script("wpmvc-general", plugin_dir_url(__FILE__) . "/js/general.js", array('jquery', 'jquery.numeric'));
    wp_enqueue_script("angular", plugin_dir_url(__FILE__) . "/js/lib/angular.min.js", array('jquery'), false, false);
    wp_enqueue_script("angular.dialog", plugin_dir_url(__FILE__) . "/js/lib/ngDialog.min.js", array('angular'), false, false);
    wp_enqueue_script("angular.animate", plugin_dir_url(__FILE__) . "/js/lib/angular-animate.min.js", array('angular'), false, false);
    
    wp_enqueue_script("user-script", plugin_dir_url(__FILE__) . "/js/script.js", array('angular'), false, false);
    
    wp_enqueue_style('angular.dialog-style', plugin_dir_url(__FILE__) . "/css/ngDialog.min.css");
    wp_enqueue_style('angular.dialog.theme-style', plugin_dir_url(__FILE__) . "/css/ngDialog-theme-default.min.css");
    wp_enqueue_style('user-style', plugin_dir_url(__FILE__) . "/css/style.css");
});
add_action( 'admin_enqueue_scripts', function(){
    wp_enqueue_script("wpmvc-general", plugin_dir_url(__FILE__) . "/js/general.js", array('jquery'));
    wp_enqueue_script("jquery.numeric", plugin_dir_url(__FILE__) . "/js/lib/jquery.numeric.min.js", array('jquery'));
    wp_enqueue_script("angular", plugin_dir_url(__FILE__) . "/js/lib/angular.min.js", array(), false, false);
    wp_enqueue_script("angular.animate", plugin_dir_url(__FILE__) . "/js/lib/angular-animate.min.js", array('angular'), false, false);
    wp_enqueue_script("angular.dialog", plugin_dir_url(__FILE__) . "/js/lib/ngDialog.min.js", array('angular'), false, false);
    
    wp_enqueue_script("admin-script", plugin_dir_url(__FILE__) . "/js/script.js", array('angular'), false, false);
    
    wp_enqueue_style('angular.dialog-style', plugin_dir_url(__FILE__) . "/css/ngDialog.min.css");
    wp_enqueue_style('angular.dialog.theme-style', plugin_dir_url(__FILE__) . "/css/ngDialog-theme-default.min.css");
    wp_enqueue_style('admin-style', plugin_dir_url(__FILE__) . "/css/style.css");
});

//===============================Setup admin menu===============================
add_action('admin_menu', function(){
    $menuManager = new PluginMVCMenuPages("Kalendarz");

    $menuParent = $menuManager->InsertPage("Kalendarz", "Callendar", "Manage", "Kalendarz");
});
