<?php

namespace WpTodo\Base;
    
abstract class Shortcode extends Executable {
    public static abstract function get_wp_name();
    public abstract function execute();
}
