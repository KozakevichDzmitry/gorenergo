<?php


namespace App\filter;

use App\ACF\Acf;

final class FilterOptions {
    public static function init() {
        if( function_exists( 'acf_add_options_page' ) ){
            acf_set_options_page_menu( __('Options', TM_TEXTDOMAIN) );
            acf_add_options_page();
            acf_add_options_sub_page( __('Filter options', TM_TEXTDOMAIN) );
        }
    }
}