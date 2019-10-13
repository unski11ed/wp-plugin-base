import { stringify } from 'query-string';

function todosService(wpPluginCall) {
    return {
        fetchTodos: function(statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'get_todos')
                .then(function(response) {
                    statusChangeCb('ok');

                    return response.data.todos.map(function(todo) {
                        return Object.assign({}, todo, {
                            isComplete: todo.complete === '1'
                        })
                    });
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        },
        createTodo: function(content, statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'add_todo', { content: content })
                .then(function(result) {
                    statusChangeCb(result.data.status, result.data.message);

                    return result.data.status === 'ok';
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        },
        deleteTodo: function(id, statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'delete_todo', { id: id })
                .then(function(result) {
                    statusChangeCb(result.data.status, result.data.message);

                    return result.data.status === 'ok'
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        },
        toggleComplete: function(id, isComplete, statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'toggle_complete', { id: id, is_complete: isComplete })
                .then(function(result) {
                    statusChangeCb(result.data.status, result.data.message);

                    return result.data.status === 'ok'
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        }
    };
}

todosService.$inject = ['todo.wpPluginCallService'];

export { todosService };
