<?php

namespace EASY_COVERAGE_AREA_MAPS\Admin;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;
use \Carbon_Fields\Carbon_Fields;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;
use EASY_COVERAGE_AREA_MAPS\Core\WordPressHooks;

if (!defined('ABSPATH')) exit;

class CustomFields
{

    public function register()
    {

        // init carbon fields
        add_action('after_setup_theme', [$this, 'load_carbon_fields']);

        // register fields and containers
        add_action('carbon_fields_register_fields', [$this, 'register_carbon_fields']);

        // hide rich text meta buttons
        add_action('crb_media_buttons_html', fn($html, $field_name) => ($field_name === Functions::prefix('desc') ? null : $html), 12, 2);
    }

    /**
     * init carbon fields
     */
    public function load_carbon_fields()
    {
        Carbon_Fields::boot();
    }

    /**
     * register fields and containers
     */
    public function register_carbon_fields()
    {
        self::register_coverage_map_custom_fields();
        self::register_coverage_status_custom_fields();
        self::register_settings_page();
    }


    public static function register_coverage_map_custom_fields()
    {
        $container = Container::make('post_meta', __('Map Settings'));
        $container->where('post_type', '=', Constant::CPT_CUSTOM_AREA_MAPS);
        $container->add_fields([
            Field::make('complex', Functions::prefix('regions'), __('Regions'))
                ->set_required(true)
                ->set_collapsed(true)
                ->set_min(1)
                ->setup_labels(['plural_name' => 'Regions', 'singular_name' => 'Region'])
                ->add_fields([
                    Field::make('text', Functions::prefix('title'), __('Region Title'))
                        ->set_required(true),
                    Field::make('select', Functions::prefix('status'), __('Region Status'))
                        ->set_options(WordPressHooks::get_statuses_as_options())
                        ->set_required(true),
                    Field::make('file', Functions::prefix('coordinates'), __('Coordinates'))
                        ->set_required(true)
                        ->set_type(['json', 'geojson']),
                ])
                ->set_header_template(Functions::get_template('admin/metaboxes/headers/regions.tmpl.php')),
        ]);
    }


    public static function register_coverage_status_custom_fields()
    {
        $container = Container::make('post_meta', __('Map Settings'));
        $container->where('post_type', '=', Constant::CPT_CUSTOM_STATUS);
        $container->add_fields([
            Field::make('rich_text', Functions::prefix('desc'), __('Status Description'))
                ->set_required(true),
            Field::make('color', Functions::prefix('color'), __('Status Color'))
                ->set_required(true),
        ]);
    }


    public static function register_settings_page()
    {
        $container = Container::make('theme_options', __('Settings'));
        $container->set_page_parent(Constant::SLUG_ADMIN_MENU);
        $container->set_page_file(Constant::SLUG_ADMIN_MENU_SETTINGS);
        $container->add_tab(__('Settings'), array());
        $container->add_tab(__('Radar'), array(
            Field::make('radio', Functions::prefix('api_mode'), __('API Mode'))
                ->set_options([
                    'test' => 'Test',
                    'live' => 'Live',
                ])
                ->set_default_value('test')
                ->set_required(true),

            // 
            Field::make('text', Functions::prefix('test_secret'), 'Test Secret')
                ->set_attribute('type', 'password')
                ->set_required(true)
                ->set_conditional_logic(array(
                    [
                        'field' => Functions::prefix('api_mode'),
                        'value' => 'test'
                    ]
                )),

            // 
            Field::make('text', Functions::prefix('test_publishable'), 'Test Publishable')
                ->set_attribute('type', 'password')
                ->set_required(true)
                ->set_conditional_logic(array(
                    [
                        'field' => Functions::prefix('api_mode'),
                        'value' => 'test'
                    ]
                )),

            // 
            Field::make('text', Functions::prefix('live_secret'), 'Live Secret')
                ->set_attribute('type', 'password')
                ->set_required(true)
                ->set_conditional_logic(array(
                    [
                        'field' => Functions::prefix('api_mode'),
                        'value' => 'live'
                    ]
                )),

            // 
            Field::make('text', Functions::prefix('live_publishable'), 'Live Publishable')
                ->set_attribute('type', 'password')
                ->set_required(true)
                ->set_conditional_logic(array(
                    [
                        'field' => Functions::prefix('api_mode'),
                        'value' => 'live'
                    ]
                )),
        ));
    }
}
