<?php

function oceanwp_child_enqueue_parent_style() {
	wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'oceanwp_child_enqueue_parent_style' );
add_action( 'after_setup_theme', 'true_remove_wc_gallery_lightbox', 25 );
 
function true_remove_wc_gallery_lightbox() { 
	remove_theme_support( 'wc-product-gallery-lightbox' );
}
add_filter( 'woocommerce_product_subcategories_hide_empty', '__return_false' );
add_filter( 'get_terms', 'ts_get_subcategory_terms', 10, 3 );
function ts_get_subcategory_terms( $terms, $taxonomies, $args ) {
  $new_terms = array();
	if ( in_array( 'product_cat', $taxonomies ) && ! is_admin() &&is_shop() ) {
		foreach( $terms as $key => $term ) {
			if ( !in_array( $term->slug, array( 'misc' ) ) ) { //ваш слаг категории
				$new_terms[] = $term;
			}
		}
	$terms = $new_terms;
	}
	return $terms;
}
