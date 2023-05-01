<?php

namespace App\Base;

final class Enqueue
{

    public function __construct()
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueueStyles']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueueScripts']);

    }

    public static function enqueueStyles()
    {
        wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/main.min.css', array(), self::fileTimeJsCss('main', 'css'));
    }

    public static function enqueueScripts()
    {
        wp_deregister_script( 'oceanwp-infinite-scroll' );
        wp_enqueue_script( 'oceanwp-infinite-scroll', get_stylesheet_directory_uri() . '/assets/js/ow-infinite-scroll.min.js', [], true );

    }
    public static function fileTimeJsCss($fileName, $typeCssOrJS)
    {
        switch ($typeCssOrJS) {
            case "css":
                return filemtime(get_theme_file_path('/assets/css/' . $fileName . '.min.css'));
            case "js":
                return filemtime(get_theme_file_path('/assets/js/' . $fileName . '.min.js'));
            default:
                return '1';
        }
    }
}