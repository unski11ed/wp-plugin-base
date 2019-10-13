import angular from 'angular';

import { wpPluginCallService } from './wpPluginCall.service';
import { configurationService } from './configuration.service';
import { todosService } from './todos.service';

var moduleName = 'todo.services';

angular.module(moduleName, [])
    // TODO: Attach services here
    .service('todo.wpPluginCallService', wpPluginCallService)
    .service('todo.configurationService', configurationService)
    .service('todo.todosService', todosService)

export default moduleName;
