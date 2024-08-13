<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

class AjaxHandler
{
    public function register()
    {
        // get regions defined for a map
        add_action('wp_ajax_ecap_get_map_regions', [$this, 'ajax_get_map_regions']);
    }


    public function ajax_get_map_regions()
    {
        check_ajax_referer('ajax_security', 'security');

        $post_id = isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : null;
        if (empty($post_id)) {
            wp_send_json_error(['message' => 'Invalid input, required fields missing.']);
            wp_die();
        }

        // TODO remove debug code
        $regions = [
            ['id' => 1, 'title' => 'Region 1', 'points' => [[51.509, -0.08], [51.503, -0.06], [51.51, -0.047]]],
            ['id' => 2, 'title' => 'Region 2', 'points' => [[51.509, -0.08], [51.503, -0.06], [51.51, -0.047]]],
            ['id' => 3, 'title' => 'Region 3', 'points' => [[51.509, -0.08], [51.503, -0.06], [51.51, -0.047]]],
        ];

        if (empty($regions)) {
            wp_send_json_error(['message' => 'Unable to create new import job.']);
            wp_die();
        }

        wp_send_json_success(['regions' => $regions]);
        wp_die();
    }
}
