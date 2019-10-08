<?php

namespace WpTodo\Base;
namespace __PLuginNamespace__\Model;

class Todos extends Controller {
    private $_todos;

    public function __construct($url_data, $model_data){
        parent::__construct($url_data, $model_data);
        
        $this->_todos = new TodosRepository();
    }

    public function action__manage(){
		$todos = $this->_todos->get_all();

        return $this->view(
            "todos_controller/manage",
			array(
				'todos' => $todos,
			)
        );
    }

    public function ajax__public__get_todos() {
        $todos = $this->_todos->get_all();

        return json_encode(
            array(
                'todos' => $todos
            )
        );
    }

    public function ajax__public__add_todo() {
        $content = $this->view_model['content'];
        
        $result = $this->_todos->create($content);

        if ($result) {
            json_encode(
                array(
                    'status' => 'success'
                )
            );
        }
        return json_encode(
            array(
                'status' => 'error',
                'message' => 'Backend: Failed to create a TODO - database error'
            )
        );
    }

    public function ajax__public__toggle_complete() {
        $id = $this->view_model['id'];
        $is_complete = $this->view_model['is_complete'];

        $result = $this->_todos->toggle_complete($id, $is_complete);

        return json_encode(
            array(
                'status' => $result ? 'success' : 'error'
            )
        );
    }

    public function ajax__public__delete_todo() {
        $id = $this->view_model['id'];

        $result = $this->_todos->remove($id);

        return json_encode(
            array(
                'status' => $result ? 'success' : 'error'
            )
        );
    }
}
