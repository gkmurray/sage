<?php

namespace App\Lib;

use App\Lib\Error;

class AcfLoader
{

    /**
     * The current singleton instance (if any)
     *
     * @var object|boolean
     */
    private static $instance = false;

    /**
     * Path to ACF directory
     *
     * @var string
     */
    private $acf_path;

    /**
     * URI to ACF directory
     *
     * @var string
     */
    private $acf_dir;

    /**
     * Whether to hide the admin menu
     *
     * @var boolean
     */
    private $hide_admin_menu = false;

    public function __construct()
    {
        $this->acf_path = get_stylesheet_directory().'/../vendor/advanced-custom-fields/advanced-custom-fields-pro/';
        $this->acf_dir = get_stylesheet_directory_uri().'/../vendor/advanced-custom-fields/advanced-custom-fields-pro/';

        // Check for and include ACF Pro files
        if (!class_exists('acf') && file_exists($this->acf_path. 'acf.php')) {
            include_once($this->acf_path . 'acf.php');

            // Set ACF path
            add_filter('acf/settings/path', function ($path) {
                return $this->acf_path;
            });

            // Set ACF dir
            add_filter('acf/settings/dir', function ($dir) {
                return $this->acf_dir;
            });

            // Hide ACF menu item
            if ($this->hide_admin_menu) {
                add_filter('acf/settings/show_admin', '__return_false');
            }
        } elseif (!class_exists('acf')) {
            // Display warning if ACF is not installed
            add_action('admin_notices', function () {
                $class = 'notice notice-error';
                $message = __('ACF not activated. Make sure you activate the Advanced Custom Fields plugin in the
                    WordPress admin area, or include the files in the vendor folder.', 'sage');
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            });

            add_filter('template_include', function ($template) {
                new Error(
                    __('Make sure you activate the Advanced Custom Fields plugin in the WordPress admin area, or include
                    the files in the vendor folder.', 'sage'),
                    __('Advanced Custom Fields not found', 'sage')
                );
            });
        }
    }

    /**
     * Retrieve the single AcfLoader instance
     *
     * @return object  this
     */
    public static function get_instance()
    {
        if (self::$instance === false) {
            self::$instance = new AcfLoader;
        }

        return self::$instance;
    }

    /**
     * Register actions
     *
     * @return object  this
     */
    public function register_actions()
    {
        /**
         * Create a Theme Options page for ACF fields
         */
        add_action('acf/init', function () {
            if (function_exists('acf_add_options_page')) {
                $parent = acf_add_options_page([
                    'page_title' => __('Theme Options', 'sage'),
                    'menu_title' => __('Theme Options', 'sage'),
                    'menu_slug' => 'theme_options',
                    'capability' => 'manage_options',
                    'post_id' => 'theme_options',
                ]);
            }
        });

        /**
         * Add Google API Key here
         *
         * @todo Load API key from envar
         */
        add_action('acf/init', function () {
            acf_update_setting('google_api_key', '');
        });

        return self::$instance;
    }

    /**
     * Register custom locations rules for determining where to display fields
     *
     * @link https://www.advancedcustomfields.com/resources/custom-location-rules/
     * @return void
     */
    public function register_custom_location_rules()
    {
        /**
         * Add Post Category Ancestor rule for evaluating on Posts with a common Parent Category
         */
        add_filter('acf/location/rule_types', function ($choices) {
            if (!isset($choices['Post']['post_category_ancestor'])) {
                $choices['Post']['post_category_ancestor'] = 'Post Category Ancestor';
            }
            return $choices;
        });
        add_filter('acf/location/rule_values/post_category_ancestor', function ($choices) {
            // copied from acf rules values for post_category
            $terms = acf_get_taxonomy_terms('category');
            if (!empty($terms)) {
                $choices = array_pop($terms);
            }
            return $choices;
        });
        add_filter('acf/location/rule_match/post_category_ancestor', function ($match, $rule, $options) {
            // most of this copied directly from acf post category rule
            $terms = $options['post_taxonomy'];
            $data = acf_decode_taxonomy_term($rule['value']);
            $term = get_term_by('slug', $data['term'], $data['taxonomy']);
            if (!$term && is_numeric($data['term'])) {
                $term = get_term_by('id', $data['term'], $data['taxonomy']);
            }
            // this is where it's different than ACf
            // get terms so we can look at the parents
            if (is_array($terms)) {
                foreach ($terms as $index => $term_id) {
                    $terms[$index] = get_term_by('id', intval($term_id), $term->taxonomy);
                }
            }
            if (!is_array($terms) && $options['post_id']) {
                $terms = wp_get_post_terms(intval($options['post_id']), $term->taxonomy);
            }
            if (!is_array($terms)) {
                $terms = array($terms);
            }
            $terms = array_filter($terms);
            $match = false;
            // collect a list of ancestors
            $ancestors = array();
            if (count($terms)) {
                foreach ($terms as $term_to_check) {
                    $ancestors = array_merge(get_ancestors($term_to_check->term_id, $term->taxonomy));
                } // end foreach terms
            } // end if
            // see if the rule matches any term ancetor
            if ($term && in_array($term->term_id, $ancestors)) {
                $match = true;
            }

            if ($rule['operator'] == '!=') {
                // reverse the result
                $match = !$match;
            }
            return $match;
        }, 10, 3);

        return self::$instance;
    }
}
