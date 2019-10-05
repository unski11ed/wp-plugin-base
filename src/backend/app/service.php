<?php

namespace __PluginNamespace__;
namespace __PluginNamespace__\Base;

includePhpFiles(plugin_dir_path(__FILE__)."/../base");
includePhpFiles(plugin_dir_path(__FILE__)."/model");
includePhpFiles(plugin_dir_path(__FILE__)."/controller");
includePhpFiles(plugin_dir_path(__FILE__)."/shortcode");

class PluginMVCMenuPages { 
    private $slug_prefix;
    private $plugin_name;
    
    public function __construct($plugin_name, $slug_prefix = ""){
        $this->plugin_name = $plugin_name;
        $this->slug_prefix = $slug_prefix;
    }
    
    public function insert_page($page_name, $controller, $action, $menu_name = "", $params = "") {
        $slug = $this->slugPrefix . $this->normalizeString($page_name);
        
        add_menu_page(
                $page_name, 
                empty($menu_name) ? $page_name : $menu_name, 
                'manage_options', 
                $slug, 
                function() use($controller, $action, $params) {
                    if(empty($controller) || empty($action)){
                        return;
                    }
                    do_action(strtolower($controller) . "-" . strtolower($action), $params);
                }
            ); 
        
        return $slug;
    }
    
    public function insert_sub_page($parent_slug, $sub_page_name, $controller, $action, $params = "") {
        $slug = $this->slugPrefix . $this->normalizeString($sub_page_name);
        
        add_submenu_page( 
                $parent_slug, 
                $sub_page_name, 
                $sub_page_name, 
                'manage_options',
                $slug, 
                function() use($controller, $action, $params){
                    if(empty($controller) || empty($action)){
                        return;
                    }
                    do_action(strtolower($controller) . "-" . strtolower($action), $params);
                }
            );
        
        return $slug;
    }
}
    
class PluginMVCService {
    public static function execute($controller_name, $action_name) {
        $controller = new $controller_name($_GET, $_POST);

        $action_name = "action__$action_name";

        echo $controller->$action_name();
    }

    public static function execute_ajax($controller_name, $action_name, $access) {
        $json_data = stripcslashes($_POST['data']);
        $controller = new $controller_name($_GET, json_decode($json_data, true));

        $action_name = "ajax__{$access}__{$action_name}";

        echo $controller->$action_name();

        die();
    }

    public static function register_routes() {
        $controllers = get_implementing_classes("Controller");

        $register_action = function($controller, $action) {
            $action_name = strtolower($controller) . "-" .  strtolower($action);
            add_action($action_name, function() use ($controller, $action){
                PluginMVCService::Execute($controller, $action);
            });
        };

        $register_ajax = function($controller, $access, $action) {
            $action_name = strtolower($controller) . "-" .  strtolower($action);

            $exec_function = function() use ($controller, $action, $access) {
                PluginMVCService::ExecuteAjax($controller, $action, $access);
            };

            add_action("wp_ajax_$action_name", $exec_function);
            if ($access === 'public') {
                add_action("wp_ajax_nopriv_$action_name", $exec_function);
            }
        };

        foreach ($controllers as $controller) {
            $methods = get_class_methods($controller);

            foreach($methods as $method){
                $params = explode("__", $method);

                if (count($params) == 3 || count($params) == 2) {
                    switch ($params[0]) {
                        case 'action':
                            $register_action($controller, strtolower($params[1]));
                        break;

                        case 'ajax':
                            $register_ajax($controller, $params[1], $params[2]);
                        break;
                    }
                }
            }
        }
    }

    function register_shortcodes(){
        $shortcodes = get_implementing_classes("Shortcode");

        foreach ($shortcodes as $shortcode_name) {
            add_shortcode($shortcode_name, function($attributes) use ($shortcode_name){
                $shortcode = new $shortcode_name($_GET, $attributes);
                return $shortcode->execute();
            });
        }
    }
}
