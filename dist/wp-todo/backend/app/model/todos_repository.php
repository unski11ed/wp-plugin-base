<?php

namespace WpTodo\Base;
namespace WpTodo\Model;

class TodosRepository extends Model {
    private $_db;

    public static function get_table_name() {
        global $wpdb;

        return $wpdb->prefix."todo_todos";
    }

    public static function initialize() {
        global $wpdb;

        $result = $wpdb->prepare("
            CREATE TABLE %s (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                content MEDIUMTEXT NOT NULL,
                complete BIT DEFAULT 0,
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )   
        ", self::get_table_name());

        if (!$result) {
            throw new Exception("WpTodo: Filed to initialize database. Reason:" . $wpdb->print_error());
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
        $query = $this->_db->prepare(
            "SELECT * FROM %s LIMIT 50",
            self::get_table_name()
        );
        
        return $this->_db->get_results($query, ARRAY_A);
    }

    public function toggle_complete($id, $is_complete) {
        $result = $this->_db->update(
            self::get_table_name(),
            array(
                'is_complete' => intval($is_complete)
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
