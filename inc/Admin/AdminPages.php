<?php

namespace PLUGIN_NAMESPACE\Admin;

use PLUGIN_NAMESPACE\Base\Variable;

if (!defined('ABSPATH')) exit;

class AdminPages
{
    protected static $views = array();

    public function register()
    {
        /* Add Admin Page */
        add_action('admin_menu', [$this, 'add_admin_page'], -99);
    }


    public static function get_admin_pages($object_context = null)
    {
        return array(
            // defined admin pages here...
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

            $template_path = Variable::GET('TEMPLATES') . "/admin/pages/{$current_view}.php";
            if (file_exists($template_path)) {
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
}
