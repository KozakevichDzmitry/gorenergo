<?php

namespace App\ACF;

final class Acf {

	public function __construct() {
        add_filter( 'acf/load_field/name=cat_filters', array(self::class,'edit_field_cat_filters') );
        add_filter( 'acf/load_field/name=select_attribute', array(self::class,'edit_field_cat_filters') );
    }

    public static function edit_field_cat_filters( $field ) {
        $attributes = wc_get_attribute_taxonomies();
        $choices = array();
        foreach ( $attributes as $attribute ) {
            $choices[ $attribute->attribute_name ] = $attribute->attribute_label;
        }
        $field['type'] = 'select';
        $field['choices'] = $choices;
        return $field;
    }

}