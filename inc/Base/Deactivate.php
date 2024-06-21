<?php

namespace PLUGIN_NAMESPACE\Base;

if (!defined('ABSPATH')) exit;

class Deactivate
{
    public static function deactivate($__FILE__)
    {
        register_deactivation_hook($__FILE__, [get_called_class(), 'deactivation_callback']);
    }

    public static function deactivation_callback()
    {
        flush_rewrite_rules();
    }
}
