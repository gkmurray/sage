<?php

/**
 * Use this file for registering new filters
 */

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add a body class for custom posts types */
    $post = get_post();
    if (is_single() && $post->post_type !== 'post') {
        $classes[] = $post->post_type;
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Add shortcodes to body classes */
    if ($current = get_post()) {
        $shortcodes = get_shortcode_tags();

        // Use key for shortcode name, value for shortcode
        // callback function name
        foreach ($shortcodes as $shortcode => $value) {
            if (has_shortcode($current->post_content, $shortcode)) {
                $classes[] = $shortcode;
            }
        }
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return '&hellip;';
});

/**
 * Template Hierarchy should search for .blade.php files
 */
collect([
    'index', '404', 'archive', 'author', 'category', 'tag', 'taxonomy', 'date', 'home',
    'frontpage', 'page', 'paged', 'search', 'single', 'singular', 'attachment'
])->map(function ($type) {
    add_filter("{$type}_template_hierarchy", __NAMESPACE__.'\\filter_templates');
});

/**
 * Render page using Blade
 */
add_filter('template_include', function ($template) {
    $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
        return apply_filters("sage/template/{$class}/data", $data, $template);
    }, []);
    if ($template) {
        echo template($template, $data);
        return get_stylesheet_directory().'/index.php';
    }
    return $template;
}, PHP_INT_MAX);

/**
 * Tell WordPress how to find the compiled path of comments.blade.php
 */
add_filter('comments_template', function ($comments_template) {
    $comments_template = str_replace(
        [get_stylesheet_directory(), get_template_directory()],
        '',
        $comments_template
    );
    return template_path(locate_template(["views/{$comments_template}", $comments_template]) ?: $comments_template);
});

/**
 * Repace default search form with searchform.blade.php
 */
add_filter('get_search_form', function ($search_form) {
    ob_start();
    include(template_path(locate_template("views/partials/searchform.blade.php")));
    $search_form = ob_get_clean();
    return $search_form;
});

/**
 * Add attributes to post links
 */
add_filter('next_post_link', 'App\\post_link_attributes');
add_filter('previous_post_link', 'App\\post_link_attributes');

/**
 * Add custom image size to the list of selectable sizes
 */
add_filter('image_size_names_choose', function ($sizes) {
    return array_merge($sizes, array(
        'xlarge' => __('HD'),
    ));
});

/**
 * Allow SVGs to be uploaded through the Wordpress Media Library
 */
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

/**
 * Add 'async' and 'defer' attributes to specified
 * script tags
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    if (false) {
        return str_replace(' src', 'async defer src', $tag);
    }
    return $tag;
}, 10, 2);

/**
* Add Foundation active class to menu
*/
add_filter('nav_menu_css_class', function ($classes, $item) {
    if ($item->current == 1 || $item->current_item_ancestor == true) {
        $classes[] = 'active';
    }
    return $classes;
}, 10, 2);

/**
 * Stop WordPress from using the sticky class (which conflicts with Foundation), and style WordPress
 * sticky posts using the .wp-sticky class instead
 */
add_filter('post_class', function ($classes) {
    if (in_array('sticky', $classes)) {
        $classes = array_diff($classes, array("sticky"));
        $classes[] = 'wp-sticky';
    }

    return $classes;
});

/**
 * Wrap embeds in Foundation's Responsive Embed class
 */
add_filter('embed_oembed_html', function ($cache, $url, $attr, $post_id) {
    return '<div class="responsive-embed widescreen">' . $cache . '</div>';
}, 10, 4);

/**
* Remove pesky injected css for recent comments widget
*/
add_filter('wp_head', function () {
    if (has_filter('wp_head', 'wp_widget_recent_comments_style')) {
        remove_filter('wp_head', 'wp_widget_recent_comments_style');
    }
});

/**
* Clean up gallery output in wp
*/
add_filter('gallery_style', function ($css) {
    // Remove injected CSS from gallery
    return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
});
