<?php
const TM_TEXTDOMAIN = 'gorenergosnab';

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}
/**
 * Initialize all the core classes of the theme
 */
if (class_exists('App\\Init')) {
    App\Init::register_services();
}

add_action('after_setup_theme', 'true_remove_wc_gallery_lightbox', 25);
function true_remove_wc_gallery_lightbox()
{
    remove_theme_support('wc-product-gallery-lightbox');
}

add_filter('woocommerce_product_subcategories_hide_empty', '__return_false');
add_filter('get_terms', 'ts_get_subcategory_terms', 10, 3);
function ts_get_subcategory_terms($terms, $taxonomies, $args)
{
    $new_terms = array();
    if (in_array('product_cat', $taxonomies) && !is_admin() && is_shop()) {
        foreach ($terms as $key => $term) {
            if (!in_array($term->slug, array('misc'))) { //ваш слаг категории
                $new_terms[] = $term;
            }
        }
        $terms = $new_terms;
    }
    return $terms;
}


add_action( 'woocommerce_after_shop_loop', 'add_btn', 11 );

function add_btn(){
    global $wp_query
    ?>
    <button class="load-more__btn"
            data-filter="false"
            data-max_pages="<?php echo $wp_query->max_num_pages;?>"
            <?php if($wp_query->max_num_pages <= 1) echo 'style="display: none;"'?> >
        <?php echo __("Загрузить еще", TM_TEXTDOMAIN) ?>
    </button>
<?php }