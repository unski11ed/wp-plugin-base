<?php

class TodosRepository extends __PluginNamespace__\Base\Model {
    private $_db;

    public static function get_table_name() {
        global $wpdb;

        return $wpdb->prefix."todo_todos";
    }

    public static function initialize() {
        global $wpdb;

        $table_name = self::get_table_name();

        $query = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            content MEDIUMTEXT NOT NULL,
            complete BIT DEFAULT 0,
            created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";

        $result = $wpdb->query($query);

        if (!$result) {
            throw new Exception("__PluginName__: Filed to initialize database. Reason:" . $wpdb->print_error());
        }
    }

    public function __construct(){
        global $wpdb;
        $this->_db = $wpdb;
    }
    
    public function create($content) {
        $result = $this->_db->insert(
            self::get_table_name(), 
            array(
				'content' => $content,
            )
        );

        return $result;
    }

    public function get_all() {
        $table_name = self::get_table_name();

        $query = "SELECT * FROM $table_name LIMIT 50;";
        
        return $this->_db->get_results($query, ARRAY_A);
    }

    public function toggle_complete($id, $is_complete) {
        $result = $this->_db->update(
            self::get_table_name(),
            array(
                'complete' => intval($is_complete)
            ),
            array(
                'id' => intval($id)
            ),
            array(
                '%d'
            ),
            array(
                '%d'
            )
        );
    
        return $result;
    }

    public function remove($id) {
        return $this->_db->delete(
            self::get_table_name(),
            array(
                'id' => $id
            ),
            array(
                '%d'
            )
        );  
    }
}
