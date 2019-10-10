import angular from 'angular';

import { todoController } from './todo.controller';

var moduleName = 'todo.controllers';

angular.module(moduleName, [])
    // TODO: Attach controllers here
    .controller('todo.todoController', todoController);

export default moduleName;
