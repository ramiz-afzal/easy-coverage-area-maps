<?php

namespace PLUGIN_NAMESPACE\Base;

use PLUGIN_NAMESPACE\Base\Functions;

if (!defined('ABSPATH')) exit;

class Activate
{
    public static function activate($__FILE__)
    {
        register_activation_hook($__FILE__, [get_called_class(), 'activation_callback']);
    }

    public static function activation_callback()
    {
        Functions::register_uuid();
        flush_rewrite_rules();
    }
}
