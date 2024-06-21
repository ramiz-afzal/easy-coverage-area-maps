<?php

/**
 * Plugin Name:       Plugin Name
 * Plugin URI:        mailto:m.ramiz.afzal@gmail.com
 * Description:       Plugin Description
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Ramiz Afzal
 * Author URI:        mailto:m.ramiz.afzal@gmail.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Direct access protection
defined('ABSPATH') or die();

// composer autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// load global variables
if (class_exists('PLUGIN_NAMESPACE\\Base\\Variable')) {
    PLUGIN_NAMESPACE\Base\Variable::LOAD_VARIABLES(__FILE__);
}

// plugin activation callback
if (class_exists('PLUGIN_NAMESPACE\\Base\\Activate')) {
    PLUGIN_NAMESPACE\Base\Activate::activate(__FILE__);
}

// plugin deactivation callback
if (class_exists('PLUGIN_NAMESPACE\\Base\\Deactivate')) {
    PLUGIN_NAMESPACE\Base\Deactivate::deactivate(__FILE__);
}

// load plugin services
if (class_exists('PLUGIN_NAMESPACE\\Init')) {
    PLUGIN_NAMESPACE\Init::register_services();
}
