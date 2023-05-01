<?php

namespace App\filter;

use App\Base\Enqueue;
use WP_Query;
use WP_Term;
use WP_Widget;

class FilterWidget extends WP_Widget
{
    const FILTER_ACTION = 'filter';

    function __construct()
    {
        parent::__construct(
            'filter_products',
            __('Filter Widget'),
            array('description' => __('Filter Widget Description'),)
        );
        global $wp_query;
        wp_enqueue_style('filter', get_stylesheet_directory_uri() . '/assets/css/filter.min.css', array('main'), Enqueue::fileTimeJsCss('filter', 'css'));
        wp_enqueue_script('filter', get_stylesheet_directory_uri() . '/assets/js/filter.min.js', array(), Enqueue::fileTimeJsCss('filter', 'js'), true);
        wp_localize_script('filter', 'filter', array(
            'action' => self::FILTER_ACTION,
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
        add_action('wp_ajax_' . self::FILTER_ACTION, array(__CLASS__, 'applyFilter'));
        add_action('wp_ajax_nopriv_' . self::FILTER_ACTION, array(__CLASS__, 'applyFilter'));
    }

    public function widget($args, $instance)
    {
        extract($args);
        $category = get_queried_object();
        if ($category instanceof WP_Term) {
            $category_id = $category->term_id;
            $title = apply_filters('widget_title', $instance['title']);
            if (!empty($category_id)) {
                echo $before_widget;
                $this->render_filters($category, $title);
                echo $after_widget;
            }
        }
    }

    public function render_filters($category, $title)
    {
        $category_id = $category->term_id;
        $filters = get_field('cat_filters', 'category_' . $category_id);
        $arr_filter_options = $this->getFilterOptions();
        if (!empty($filters)) {
            $used_attributes = $this->get_used_attributes($category);
            $all_attributes = $this->get_all_attributes();
            if ($title) {
                echo '<button type="button" class="filter__header-btn" id="btnToggleFilter" data-category_id="'.$category_id .'">' . $title . '</button>';
            }
            include_once get_stylesheet_directory() . '/filter-templates/filter-loop.php';
        }

    }

    public function get_used_attributes($category): array
    {
        $product_args = array(
            'post_status' => 'publish',
            'limit' => -1,
            'category' => $category->slug,
        );

        $products = wc_get_products($product_args);

        $used_attributes = array();
        foreach ($products as $product) {
            $product_attributes = $product->get_attributes();
            foreach ($product_attributes as $attribute) {
                $used_attributes[$attribute['name']]['id'] = $attribute['id'];
                foreach ($attribute['options'] as $option_id) {
                    $used_attributes[$attribute['name']]['options'][$option_id] = [
                        'name' => get_term($option_id)->name,
                        'slug' => get_term($option_id)->slug,
                    ];
                }
            }
        }
        return $this->sort_options_by_name($used_attributes);
    }

    public function get_all_attributes(): array
    {
        $attributes = wc_get_attribute_taxonomies();
        $all_attributes = array();
        if ($attributes) {
            foreach ($attributes as $attribute) {
                $att_slug = 'pa_' . $attribute->attribute_name;
                $att_id = $attribute->attribute_id;
                $options = get_terms(array(
                    'taxonomy' => $att_slug,
                    'hide_empty' => false,
                ));

                $all_attributes[$att_slug]['id'] = $att_id;
                $all_attributes[$att_slug]['options'] = [];
                foreach ($options as $option) {
                    $all_attributes[$att_slug]['options'][$option->term_id] = [
                        'name' => $option->name,
                        'slug' => $option->slug,
                    ];
                }
            }
        }

        return $this->sort_options_by_name($all_attributes);
    }

    public function sort_options_by_name($attributes)
    {
        foreach ($attributes as $key => $attribute) {
            usort($attributes[$key]['options'], function ($a, $b) {
                return $a['name'] <=> $b['name'];
            });
        }
        return $attributes;
    }

    public function getFilterOptions(): array
    {
        $options = get_field('selecting_filter_type', 'options');
        $acc_array = [];
        foreach ($options as $option) {
            $acc_array[$option['select_attribute']] = $option;
        }
        return $acc_array;
    }

    public static function applyFilter()
    {
        $default_post_per_page = 9;
        $shop_posts_per_page = get_theme_mod('ocean_woo_shop_posts_per_page');
        $loaded_posts = (int)$_POST['loaded_posts'];
        if($shop_posts_per_page){
            $posts_per_page =  $loaded_posts + $shop_posts_per_page;
        }else{
            $posts_per_page =  $loaded_posts + $default_post_per_page;
        }


        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => $_POST['cat_id'],
                ),
            )
        );

        $tax_query_checkbox = self::parseQueryArgs($_POST['in']);
        $tax_query_range = self::parseRangeQueryArgs($_POST['range']);
        if (!empty($tax_query_checkbox)) $args['tax_query'][] = $tax_query_checkbox;
        if (!empty($tax_query_range)) $args['tax_query'][] = $tax_query_range;

        global $wp_query;
        $wp_query = new WP_Query($args);
        $found_posts = $wp_query->found_posts;
        ob_start();
        woocommerce_product_loop_start();

        while (have_posts()) {
            the_post();
            do_action('woocommerce_shop_loop');
            wc_get_template_part('content', 'product');
        }

        woocommerce_product_loop_end();

        $posts = ob_get_clean();
        wp_reset_postdata();

        wp_send_json(['found_posts'=>$found_posts, 'loaded_posts' => $posts_per_page, 'post' => $posts,]);
    }

    public function form($instance)
    {
        $title = esc_attr($instance['title']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
        </p>
        <?php
    }

    public static function parseQueryArgs($array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[] = array(
                'taxonomy' => $key,
                'terms' => explode(",", $value),
                'field' => 'slug',
                'operator' => 'IN'
            );
        }
        return $result;
    }

    public static function parseRangeQueryArgs($array)
    {
        $result = [];
        $wp_query = '';
        foreach ($array as $key => $value) {
            $min = min($value);
            $max = max($value);
            $terms = array_filter(self::getAllValuesTaxonomy($key), function ($term) use ($min, $max) {
                return $term >= $min && $term <= $max;
            });
        }

        foreach ($array as $key => $value) {
            $min = min($value);
            $max = max($value);
            $terms = array_filter(self::getAllValuesTaxonomy($key), function ($term) use ($min, $max) {
                return $term >= $min && $term <= $max;
            });
            $result[] = array(
                'taxonomy' => $key,
                'terms' => $terms,
                'field' => 'slug',
                'operator' => 'IN'
            );
        }
        return $result;
    }

    public static function getAllValuesTaxonomy($taxonomy)
    {
        $values = [];
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => true,
        ));
        foreach ($terms as $term) {
            $values[] = $term->name;
        }
        return $values;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

}