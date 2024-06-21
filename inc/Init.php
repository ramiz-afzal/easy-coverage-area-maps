<?php

namespace PLUGIN_NAMESPACE;

if (!defined('ABSPATH')) exit;

final class Init
{
    public static function get_services()
    {
        return array(
            Core\Shortcode::class,
            Core\AjaxHandler::class,
            Core\WordPressHooks::class,
            Admin\AdminPages::class,
        );
    }

    public static function register_services()
    {
        foreach (self::get_services() as $class) {

            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    private static function instantiate($class)
    {
        return new $class();
    }
}
