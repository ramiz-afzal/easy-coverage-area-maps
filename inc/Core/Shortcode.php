<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;
use EASY_COVERAGE_AREA_MAPS\Base\Enqueue;

class Shortcode
{
    public function register()
    {
        add_shortcode('ecap_map', [$this, 'render_ecap_map']);
    }

    public function render_ecap_map($atts = [], $content = null, $shortcode_tag = null)
    {
        extract(shortcode_atts(array(
            'id' => null,
        ), $atts));

        if (empty($id)) {
            return Functions::get_template('shortcodes/default/error-message.php', ['message' => "'id' parameter is required"]);
        }

        $post = get_post($id);
        if (empty($post)) {
            return Functions::get_template('shortcodes/default/error-message.php', ['message' => "Invalid Map ID: {$id}"]);
        }

        Enqueue::enqueue_file_by_handle(Functions::with_uuid('frontend-styles'));
        Enqueue::enqueue_file_by_handle(Functions::with_uuid('frontend-script'));
        Enqueue::enqueue_file_by_handle(Functions::with_uuid('radar-styles'));
        Enqueue::enqueue_file_by_handle(Functions::with_uuid('radar-script'));

        return Functions::get_template('shortcodes/ecap-map.php', ['id' => $id]);
    }
}
