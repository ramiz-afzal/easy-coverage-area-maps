<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Variable;

class AjaxHandler
{
    public function register()
    {
        // get regions defined for a map
        add_action('wp_ajax_ecap_get_map_regions', [$this, 'ajax_get_map_regions']);
        add_action('wp_ajax_nopriv_ecap_get_map_regions', [$this, 'ajax_get_map_regions']);

        // get regions statuses
        add_action('wp_ajax_ecap_get_region_statuses', [$this, 'ajax_get_region_statuses']);
        add_action('wp_ajax_nopriv_ecap_get_region_statuses', [$this, 'ajax_get_region_statuses']);
    }


    public function ajax_get_map_regions()
    {
        try {
            check_ajax_referer('ajax_security', 'security');

            $post_id = isset($_REQUEST['post_id']) && !empty($_REQUEST['post_id']) ? $_REQUEST['post_id'] : null;
            if (empty($post_id)) {
                wp_send_json_error(['message' => 'Invalid input, required fields missing.']);
                wp_die();
            }

            $regions = carbon_get_post_meta($post_id, Functions::prefix('regions'));
            if (empty($regions)) {
                wp_send_json_error(['message' => 'Empty or Undefined data']);
                wp_die();
            }

            foreach ($regions as &$region) {
                if (!isset($region[Functions::prefix('coordinates')]) || empty($region[Functions::prefix('coordinates')])) {
                    continue;
                }

                $file_path = get_attached_file($region[Functions::prefix('coordinates')]);
                if (!file_exists($file_path)) {
                    continue;
                }

                $file_data = file_get_contents($file_path);
                if (empty($file_data)) {
                    continue;
                }

                $file_data = json_decode($file_data, true);
                if (!isset($file_data['geometry']) || !isset($file_data['geometry']['coordinates'])) {
                    continue;
                }
                $region[Functions::prefix('coordinates')] = $file_data['geometry']['coordinates'];
            }

            $regions = json_decode(str_replace(str_replace('-', '_', Variable::GET('PREFIX')) . "_", '', json_encode($regions)), true);

            wp_send_json_success(['regions' => $regions]);
            wp_die();
        } catch (\Throwable $error) {
            Functions::debug_log("Error occurred: " . $error->getMessage());
            wp_send_json_error(['message' => $error->getMessage()]);
            wp_die();
        }
    }


    public function ajax_get_region_statuses()
    {
        try {
            check_ajax_referer('ajax_security', 'security');

            $statuses = WordPressHooks::get_statuses();

            if (empty($statuses)) {
                wp_send_json_error(['message' => 'Empty or Undefined data']);
                wp_die();
            }

            wp_send_json_success(['statuses' => $statuses]);
            wp_die();
        } catch (\Throwable $error) {
            Functions::debug_log("Error occurred: " . $error->getMessage());
            wp_send_json_error(['message' => $error->getMessage()]);
            wp_die();
        }
    }
}
