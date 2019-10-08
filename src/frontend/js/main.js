import angular from 'angular';

import controllersModuleName from './controllers';
import servicesModuleName from './services';

(function() {
    var moduleName = 'todo';

    var app = angular.module(moduleName, [
        // TODO: Add dependency modules here

        controllersModuleName,
        servicesModuleName,
    ]);

    // Bootstrap
    angular.bootstrap(
        document.getElementById('todo-app-container'),
        [moduleName]
    );
})();
