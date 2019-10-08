<?php

class TodosShortcode extends Shortcode{
    public static function get_wp_name() {
        return "todos";
    }

	private $list_name;
	
    public function __construct($url_data, $model_data) {
        parent::__construct($url_data, $model_data);   
		
		$this->list_name = $modelData["list_name"];
    }
    
    public function execute() {
        return $this->View(
            "/shortcode/todos_shortcode",
            array(
                "list_name" => $this->list_name
            )
        );
    }
}