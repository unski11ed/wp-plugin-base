<?php

namespace __PluginNamespace__\Base;

abstract class Controller extends Executable {
    protected function setup_paginator($count, $items_per_page, $url_base) {
        $this->view_bag['_page'] = !isset($_GET['p']) ? 1 : intval($_GET['p']);
        $this->view_bag['_max_page'] = ceil($count / $items_per_page);
        $this->view_bag['_url_base'] = $url_base;
    }
}
