import { stringify } from 'query-string';

function wpPluginCallService($http, config) {
    return {
        call: function(controller, action, data) {
            return $http({
                url: config.ajaxUrl,
                responseType : 'json',
                method: 'POST',
                data: stringify({
                    action: controller + '-' + action,
                    data: JSON.stringify(data)
                }),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            });
        }
    };
}

wpPluginCallService.$inject = ['$http', 'todo.configurationService'];

export { wpPluginCallService };
