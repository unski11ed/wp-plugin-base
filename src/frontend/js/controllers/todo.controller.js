function todoController($scope, todosService, templateUrlProvider) {
    // Actions ==================================
    function setStatus(status, message) {
        $scope.remoteStatus.status = status;
        $scope.remoteStatus.message = message || null;
    }

    function addTodo() {
        todosService.createTodo($scope.newTodo.content, setStatus)
            .then(function(added) {
                if (added) {
                    $scope.newTodo.content = '';

                    fetchTodos();
                }
            });
        $scope.modalVisible = false;
    }

    function fetchTodos() {
        todosService.fetchTodos(setStatus)
            .then(function(todos) {
                $scope.todos.list = todos;
            });
    }

    function toggleCompleteTodo(id) {
        const todo = $scope.todos.list.find(
            function(todo) { return todo.id === id });

        if (todo) {
            todosService.toggleComplete(id, todo.isComplete, setStatus);
        }
    }

    function deleteTodo(id) {
        todosService.deleteTodo(id, setStatus)
            .then(function(deleted) {
                if (deleted) {
                    fetchTodos();
                }
            });
    }

    // Scope Definition =========================
    $scope.todos = {
        list: [],
    };
    $scope.newTodo = {
        content: ''
    };
    $scope.remoteStatus = {
        status: 'ok',
        message: null
    };
    $scope.actions = {
        fetchTodos: fetchTodos,
        addTodo: addTodo,
        deleteTodo: deleteTodo,
        toggleCompleteTodo: toggleCompleteTodo,
        toggleModal: function() {
            $scope.newTodo.content = '';
            $scope.modalVisible = !$scope.modalVisible;
        }
    }
    $scope.modalVisible = false;

    // Bootstrap ================================
    fetchTodos();
}

todoController.$inject = [
    '$scope',
    'todo.todosService',
];

export { todoController };
