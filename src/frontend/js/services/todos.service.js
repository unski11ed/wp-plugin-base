import { stringify } from 'query-string';

function todosService(wpPluginCall) {
    return {
        fetchTodos: function(statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'get_todos')
                .then(function(response) {
                    statusChangeCb('ok');

                    return response.todos;
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        },
        createTodo: function(content, statusChangeCb) {
            statusChangeCb('loading');
    
            return wpPluginCall.call('todos', 'add_todo', { content: content })
                .then(function(result) {
                    statusChangeCb(result.status, result.message);
                    
                    return result.status === 'ok';
                })
                .catch(function(err) {
                    statusChangeCb('error', err.message);
                });
        },
        deleteTodo: function(id, statusChangeCb) {
            setStatus('loading');
    
            wpPluginCall.call('todos', 'delete_todo', { id: id })
                .then(function(result) {
                    statusChangeCb(result.status, result.message);

                    return result.status === 'ok'
                })
                .catch(function(err) {
                    setStatus('error', err.message);
                });
        },
        toggleComplete: function(id, isComplete, statusChangeCb) {
            setStatus('loading');
    
            wpPluginCall.call('todos', 'toggle_complete', { id: id, is_complete: isComplete })
                .then(function(result) {
                    statusChangeCb(result.status, result.message);

                    return result.status === 'ok'
                })
                .catch(function(err) {
                    setStatus('error', err.message);
                });
        }
    };
}

todosService.$inject = ['todo.wpPluginCallService'];

export { todosService };
