<?php

namespace App\Controllers;

use Sober\Controller\Controller;

class App extends Controller
{
    /**
     * Returns the site name
     *
     * @return string|false
     */
    public function site_name()
    {
        return get_bloginfo('name');
    }

    /**
     * Returns the pagination settings
     *
     * @return array|false
     */
    public function pagination()
    {
        $pages_to_show = 7;
        $first_text = __('First', 'sage');
        $last_text = __('Last', 'sage');

        $wp_query = \get_wp_query(); // Fetch the global wp_query object
        $request = $wp_query->request;
        $posts_per_page = intval(get_query_var('posts_per_page'));
        $paged = intval(get_query_var('paged'));
        $numposts = $wp_query->found_posts;
        $max_page = $wp_query->max_num_pages;

        if ($numposts <= $posts_per_page) {
            return false;
        }

        if (empty($paged) || $paged == 0) {
            $paged = 1;
        }

        $pages_to_show_minus_1 = $pages_to_show - 1;
        $half_page_start = floor($pages_to_show_minus_1 / 2);
        $half_page_end = ceil($pages_to_show_minus_1 / 2);
        $start_page = $paged - $half_page_start;
        if ($start_page <= 0) {
            $start_page = 1;
        }

        $end_page = $paged + $half_page_end;
        if (($end_page - $start_page) != $pages_to_show_minus_1) {
            $end_page = $start_page + $pages_to_show_minus_1;
        }
        if ($end_page > $max_page) {
            $start_page = $max_page - $pages_to_show_minus_1;
            $end_page = $max_page;
        }
        if ($start_page <= 0) {
            $start_page = 1;
        }

        $pagination = [
            'start_page' => $start_page,
            'end_page' => $end_page,
            'pages_to_show' => $pages_to_show,
            'paged' => $paged,
            'max_page' => $max_page,
            'first_text' => $first_text,
            'last_text' => $last_text,
        ];

        return $pagination;
    }

    /**
     * Returns the page title
     *
     * @return string
     */
    public static function title()
    {
        if (is_home()) {
            if ($home = get_option('page_for_posts', true)) {
                return get_the_title($home);
            }
            return __('Latest Posts', 'sage');
        }
        if (is_archive()) {
            return get_the_archive_title();
        }
        if (is_search()) {
            return sprintf(__('Search Results for %s', 'sage'), get_search_query());
        }
        if (is_404()) {
            return __('Not Found', 'sage');
        }
        return get_the_title();
    }
}
