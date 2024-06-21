<?php

/**
 * Plugin Name:       Easy Coverage Area Maps
 * Plugin URI:        mailto:m.ramiz.afzal@gmail.com
 * Description:       Easily display coverage areas of your services on a map
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
if (class_exists('EASY_COVERAGE_AREA_MAPS\\Base\\Variable')) {
    EASY_COVERAGE_AREA_MAPS\Base\Variable::LOAD_VARIABLES(__FILE__);
}

// plugin activation callback
if (class_exists('EASY_COVERAGE_AREA_MAPS\\Base\\Activate')) {
    EASY_COVERAGE_AREA_MAPS\Base\Activate::activate(__FILE__);
}

// plugin deactivation callback
if (class_exists('EASY_COVERAGE_AREA_MAPS\\Base\\Deactivate')) {
    EASY_COVERAGE_AREA_MAPS\Base\Deactivate::deactivate(__FILE__);
}

// load plugin services
if (class_exists('EASY_COVERAGE_AREA_MAPS\\Init')) {
    EASY_COVERAGE_AREA_MAPS\Init::register_services();
}
