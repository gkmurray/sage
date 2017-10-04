<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Template\Blade;
use Roots\Sage\Template\BladeProvider;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);
    wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery'], null, true);
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));

    /**
    * Register navigation menus
    * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
    */
    register_nav_menus([
        'menu' => __('Menu', 'sage')
    ]);

    /**
    * Add custom image sizes
    * @link https://developer.wordpress.org/reference/functions/add_image_size/
    */
    add_image_size('xlarge', 1920);
}, 20);

/**
* Cleanup Wordpress
*/
add_action('after_setup_theme', function () {
    /** Remove category feeds */
    remove_action('wp_head', 'feed_links_extra', 3);
    /** Remove post and comment feeds */
    remove_action('wp_head', 'feed_links', 2);
    /** Remove EditURI link */
    remove_action('wp_head', 'rsd_link');
    /** Remove Windows live writer */
    remove_action('wp_head', 'wlwmanifest_link');
    /** Remove index link */
    remove_action('wp_head', 'index_rel_link');
    /** Remove previous link */
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    /** Remove start link */
    remove_action('wp_head', 'start_post_rel_link', 10, 0);
    /** Remove links for adjacent posts */
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    /** Remove WP version */
    remove_action('wp_head', 'wp_generator');
}, 20);

/**
 * Updates the `$post` variable on each iteration of the loop.
 * Note: updated value is only available for subsequently loaded views, such as partials
 */
add_action('the_post', function ($post) {
    sage('blade')->share('post', $post);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });

    /**
     * Add Blade to Sage container
     */
    sage()->singleton('sage.blade', function (Container $app) {
        $cachePath = config('view.compiled');
        if (!file_exists($cachePath)) {
            wp_mkdir_p($cachePath);
        }
        (new BladeProvider($app))->register();
        return new Blade($app['view']);
    });

    /**
     * Create @asset() Blade directive
     */
    sage('blade')->compiler()->directive('asset', function ($asset) {
        return "<?= " . __NAMESPACE__ . "\\asset_path({$asset}); ?>";
    });
});
