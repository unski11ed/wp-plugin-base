import config from './../config';

function configurationService() {
    return Object.assign({}, config, window[config.pluginNamespace]);
}

export { configurationService };
