<?php

namespace EASY_COVERAGE_AREA_MAPS\Admin;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;
use \Carbon_Fields\Carbon_Fields;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;

if (!defined('ABSPATH')) exit;

class CustomFields
{

    public function register()
    {

        // init carbon fields
        add_action('after_setup_theme', [$this, 'load_carbon_fields']);

        // register fields and containers
        add_action('carbon_fields_register_fields', [$this, 'register_carbon_fields']);
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
                    Field::make('complex', Functions::prefix('coordinate_groups'), __('Coordinate'))
                        ->set_required(true)
                        ->set_collapsed(true)
                        ->set_min(3)
                        ->setup_labels(['plural_name' => 'Coordinates', 'singular_name' => 'Coordinate'])
                        ->add_fields([
                            Field::make('text', Functions::prefix('lat'), __('Latitude'))
                                ->set_width(50)
                                ->set_required(true)
                                ->set_attribute('type', 'number'),
                            Field::make('text', Functions::prefix('long'), __('Longitude'))
                                ->set_width(50)
                                ->set_required(true)
                                ->set_attribute('type', 'number'),
                        ])
                        ->set_header_template(Functions::get_template('admin/metaboxes/headers/coordinate-groups.tmpl.php')),
                ])
                ->set_header_template(Functions::get_template('admin/metaboxes/headers/regions.tmpl.php')),
        ]);
    }
}
