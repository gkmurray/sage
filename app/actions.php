<?php

/**
 * Use this file for registering new actions
 */

namespace App;

/**
* Clean up comment styles in the head
*/
add_action('wp_head', function () {
    // Remove injected CSS from recent comments widget
    $wp_widget_factory = get_wp_widget_factory();
    if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
        remove_action(
            'wp_head',
            [$wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style']
        );
    }
});

/**
 * Add custom sticky flag to the post meta data on post update
 */
 add_action('post_updated', function ($post_id) {
    $is_sticky = (isset($_POST['sticky']) && $_POST['sticky'] == 'sticky') ||
                 (isset($_GET['sticky']) && $_GET['sticky'] == 'sticky') ? 1 : 0;

    update_post_meta($post_id, 'custom_sticky', $is_sticky);
 });

/**
 * Update Main Query on Blog page to order sticky posts
 */
 add_action('pre_get_posts', function ($query) {
    if ($query->is_home() && $query->is_main_query()) {
        $query->set('ignore_sticky_posts', 1);
        $query->set('meta_key', 'custom_sticky');
        $query->set('orderby', array('meta_value_num' => 'DESC', 'date' => 'DESC'));
    }
 });

/**
 * Remove custom Sticky meta data on theme deactivation
 */
 add_action('switch_theme', function () {
    delete_metadata('post', null, 'custom_sticky', null, true);
 });

/**
 * Add custom Sticky meta data on theme activation
 */
 add_action('after_switch_theme', function () {
    $blog_posts = get_posts([
        'post_status' => 'any',
        'numberposts' => -1,
        'meta_query' => [
            [
                'key' => 'custom_sticky',
                'compare' => 'NOT EXISTS'
            ],
        ]
    ]);

    if (!empty($blog_posts)) {
        foreach ($blog_posts as $blog_post) {
            add_post_meta($blog_post->ID, 'custom_sticky', is_sticky($blog_post->ID) ? 1 : 0);
        }
    }
 });
