<?php

namespace EASY_COVERAGE_AREA_MAPS\Base;

use EASY_COVERAGE_AREA_MAPS\Base\Variable;
use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Core\RadarApi;

if (!defined('ABSPATH')) exit;

class Enqueue
{
    public function register()
    {
        if (Variable::GET('LOAD_ADMIN_FILES')) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_files'));
        }

        if (Variable::GET('LOAD_FRONTEND_FILES')) {
            add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_files'));
        }
    }


    public function enqueue_admin_files()
    {
        wp_enqueue_style(Functions::with_uuid('admin-styles'), Functions::css_file('admin.css'), [], Functions::get_uuid());
        wp_enqueue_script(Functions::with_uuid('admin-script'), Functions::js_file('admin.js'), [], Functions::get_uuid(), true);

        if (Variable::GET('LOCALIZE_ADMIN_JS_OBJECT')) {
            wp_localize_script(
                Functions::with_uuid('admin-script'),
                Variable::GET('JS_ADMIN_OBJECT_NAME'),
                ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajax_security')]
            );
        }
    }

    public function enqueue_frontend_files()
    {
        wp_register_style(Functions::with_uuid('frontend-styles'), Functions::css_file('frontend.css'), [], Functions::get_uuid());
        wp_register_script(Functions::with_uuid('frontend-script'), Functions::js_file('frontend.js'), [], Functions::get_uuid(), false);

        // Radar Scripts
        wp_register_style(Functions::with_uuid('radar-styles'), 'https://js.radar.com/v4.4.0/radar.css', [], Functions::get_uuid());
        wp_register_script(Functions::with_uuid('radar-script'), 'https://js.radar.com/v4.4.0/radar.min.js', [], Functions::get_uuid(), false);

        if (Variable::GET('LOCALIZE_JS_OBJECT')) {
            $script_variables = ['ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajax_security'), 'asset_url' => Variable::GET('URL') . 'assets/img'];
            $api_keys = RadarApi::get_api_keys();
            if (!empty($api_keys) && isset($api_keys['public_key'])) {
                $script_variables['radar_pk'] = $api_keys['public_key'];
            }
            wp_localize_script(Functions::with_uuid('frontend-script'), Variable::GET('JS_OBJECT_NAME'), $script_variables);
        }
    }

    public static function enqueue_file_by_handle(string $file_handle = null)
    {
        global $wp_scripts;
        global $wp_styles;
        if (isset($wp_scripts->registered[$file_handle])) {
            wp_enqueue_script($file_handle);
        } else if (isset($wp_styles->registered[$file_handle])) {
            wp_enqueue_style($file_handle);
        }
    }


    public static function enqueue_file(string $file_name = null)
    {
        $string_parts = explode('.', $file_name);
        if (!empty($string_parts)) {

            $handle = $string_parts[0];
            if (end($string_parts) == 'js') {
                wp_enqueue_script(Functions::with_uuid($handle), Functions::js_file($file_name), [], Functions::get_uuid(), true);
            } else if (end($string_parts) == 'css') {
                wp_enqueue_style(Functions::with_uuid($handle), Functions::css_file($file_name), [], Functions::get_uuid());
            }
        }
    }
}
