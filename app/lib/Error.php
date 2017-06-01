<?php

namespace App\Lib;

/**
* Helper class for prettying up errors
*/
class Error
{
    /**
     * @param string $message
     * @param string $subtitle
     * @param string $title
     */
    public function __construct($message, $subtitle = '', $title = '')
    {
        $title = $title ?: __('Sage &rsaquo; Error', 'sage');
        $footer = '<a href="https://roots.io/sage/docs/">roots.io/sage/docs/</a>';
        $message = "<h1>{$title}<br><small>{$subtitle}</small></h1><p>{$message}</p><p>{$footer}</p>";
        wp_die($message, $title);
    }
}
