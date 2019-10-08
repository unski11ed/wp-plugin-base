<?php

namespace WpTodo\Base;

abstract class Executable {
    protected $url_params = array();
    protected $view_model = array();
    protected $SERVER;
    
    protected $path = array();
    protected $url = array();

    protected $view_bag = array();
    protected $script_data = array();
    
    public function __construct($url_data, $model_data) {
        $this->SERVER = $_SERVER;
        
        $this->url_prams = $url_data;
        $this->view_model = $view_model;
        
        $this->setup_paths();
    }
    
    
    private function setup_paths() {
        $this->path['main'] = plugin_dir_path(__FILE__) . "..";
        $this->path['view'] = $this->path['main'] . "/app/view";
        $this->path['model'] = $this->path['main'] . "/app/model";
        
        $this->url['main'] = plugins_url('', plugin_dir_path(__FILE__));    
    }
    
    private function set_script_data(){
        $script_data = array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
        );

        $json = json_encode(
            array_merge(
                $scriptData,
                $this->ScriptData
            )
        );
        
        echo 
            "<script type='text/javascript'>" .
            "   window.WpTodo = JSON.parse('$json');".
            "</script>";
    }
    
    protected function view($name, $view_bag = array()){
        ob_start();
        
        global $VIEWBAG;
        $VIEWBAG = array_merge($this->view_bag, $view_bag);
        
        $this->set_script_data();
        require_once $this->path['view']."/$name.php";
        
        return ob_get_clean();
    }
}
