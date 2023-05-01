<?php

namespace App\filter;
use App\filter\FilterWidget;
class Filter
{
    public function __construct() {
        FilterOptions::init();
        add_action('widgets_init', array(self::class, 'wpb_load_widget'));
    }
    public static function wpb_load_widget()
    {
        register_widget('App\filter\FilterWidget');
    }
}