import angular from 'angular';

import { wpPluginCallService } from './wpPluginCall.service';

var moduleName = 'todo.services';

angular.module(moduleName)
    // TODO: Attach controllers here
    .service('todo.wpPluginCallService', wpPluginCallService);

export default moduleName;
