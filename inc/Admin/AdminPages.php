<?php

namespace EASY_COVERAGE_AREA_MAPS\Admin;

use EASY_COVERAGE_AREA_MAPS\Base\Constant;
use EASY_COVERAGE_AREA_MAPS\Base\Functions;

if (!defined('ABSPATH')) exit;

class AdminPages
{
    protected static $views = array();

    public function register()
    {
        /* Add Admin Page */
        add_action('admin_menu', [$this, 'add_admin_page'], -99);

        /* register admin pages setting fields*/
        add_action('admin_init', [$this, 'register_admin_setting_fields']);
    }


    public static function get_admin_pages($object_context = null)
    {
        return array(
            array(
                'page_title'    => 'Coverage Maps',
                'menu_title'    => 'Coverage Maps',
                'capability'    => 'manage_options',
                'menu_slug'     => Constant::SLUG_ADMIN_MENU,
                'callback'      => [$object_context, 'render_admin_page'],
                'icon_url'      => 'dashicons-location-alt',
                'position'      => 80,
            ),
        );
    }


    public static function get_current_view()
    {
        $current_filter = current_filter();
        return isset(self::$views[$current_filter]) ? self::$views[$current_filter] : null;
    }


    public function render_admin_page()
    {
        $current_view = AdminPages::get_current_view();
        if (!empty($current_view)) {
            $template_path = Functions::get_template_file("admin/pages/{$current_view}.php");
            if (!empty($template_path)) {
                require_once($template_path);
            }
        }
    }


    public function add_admin_page()
    {
        if (function_exists('add_menu_page')) {

            $admin_pages = self::get_admin_pages($this);
            if (!empty($admin_pages)) {
                foreach ($admin_pages as $page) {

                    $page_title     = isset($page['page_title']) ? $page['page_title'] : null;
                    $menu_title     = isset($page['menu_title']) ? $page['menu_title'] : null;
                    $capability     = isset($page['capability']) ? $page['capability'] : null;
                    $menu_slug      = isset($page['menu_slug']) ? $page['menu_slug'] : null;
                    $callback       = isset($page['callback']) ? $page['callback'] : null;
                    $icon_url       = isset($page['icon_url']) ? $page['icon_url'] : null;
                    $position       = isset($page['position']) ? $page['position'] : null;
                    $parent_slug    = isset($page['parent_slug']) ? $page['parent_slug'] : null;
                    $page_hook      = NULL;

                    if (!empty($parent_slug)) {
                        $page_hook = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback, $position);
                    } else {
                        $page_hook = add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);
                    }

                    if (!empty($page_hook)) {
                        self::$views[$page_hook] = $menu_slug;
                    }
                }
            }
        }
    }


    public static function get_setting_fields()
    {
        return array(
            // TODO define settings fields
            // Default Settings
            // Functions::prefix('settings') => array(
            //     Functions::prefix('routing_enabled')   => array(
            //         'type'              => 'string',
            //         'sanitize_callback' => 'sanitize_text_field',
            //         'show_in_rest'      => false,
            //         'default'           => 'no',
            //     ),
            //     Functions::prefix('intermediate_url_slug')   => array(
            //         'type'              => 'string',
            //         'sanitize_callback' => 'sanitize_text_field',
            //         'show_in_rest'      => false,
            //         'default'           => Constant::URL_EXTERNAL_SLUG,
            //     ),
            //     Functions::prefix('routing_delay')   => array(
            //         'type'              => 'int',
            //         'sanitize_callback' => 'sanitize_text_field',
            //         'show_in_rest'      => false,
            //         'default'           => Constant::ROUTING_DELAY,
            //     ),
            // ),
        );
    }


    public function register_admin_setting_fields()
    {
        $setting_fields = self::get_setting_fields();
        if (!empty($setting_fields) && is_array($setting_fields)) {
            foreach ($setting_fields as $option_group => $option_fields) {
                if (!empty($option_fields) && is_array($option_fields)) {

                    foreach ($option_fields as $field_key => $field) {

                        $field_args                         = array();
                        $field_args['type']                 = isset($field['type']) ? $field['type'] : 'string';
                        $field_args['description']          = isset($field['description']) ? $field['description'] : null;
                        $field_args['sanitize_callback']    = isset($field['sanitize_callback']) ? $field['sanitize_callback'] : 'sanitize_text_field';
                        $field_args['show_in_rest']         = isset($field['show_in_rest']) ? $field['show_in_rest'] : false;
                        $field_args['default']              = isset($field['default']) ? $field['default'] : null;

                        register_setting($option_group, $field_key, $field_args);
                    }
                }
            }
        }
    }
}
