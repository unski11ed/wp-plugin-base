<?php

namespace __PluginBase__\Base;

abstract class Model {
    public static abstract function get_table_name();
    public static abstract function initialize();
}
