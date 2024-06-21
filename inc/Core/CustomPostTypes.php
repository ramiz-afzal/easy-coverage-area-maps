<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;

class CustomPostTypes
{
    public function register()
    {
        // register custom post types
        add_action('init', [$this, 'register_custom_post_types'], -99);
    }


    public static function get_custom_post_types()
    {
        return [
            Constant::CPT_CUSTOM_AREA_MAPS => array(
                'labels'                => self::generate_cpt_labels('Maps', 'Map'),
                'public'                => false,
                'publicly_queryable'    => false,
                'hierarchical'          => false,
                'show_in_nav_menus'     => false,
                'rewrite'               => false,
                'query_var'             => false,
                'capability_type'       => 'post',
                'supports'              => ['title'],
                'has_archive'           => false,
                'show_ui'               => true,
                'exclude_from_search'   => true,
                'show_in_menu'          => Constant::SLUG_ADMIN_MENU,
            ),
        ];
    }


    /**
     * register custom post types
     */
    public function register_custom_post_types()
    {
        if (function_exists('register_post_type')) {

            $custom_post_types = self::get_custom_post_types();

            if (!empty($custom_post_types)) {
                foreach ($custom_post_types as $post_type_key => $post_type_args) {
                    register_post_type($post_type_key, $post_type_args);
                }
            }
        }
    }


    /**
     * @param string $name
     * @param string $singular_name
     */
    public static function generate_cpt_labels(string $name, string $singular_name)
    {
        $wp_labels = array(
            'name'                  => '{name}',
            'singular_name'         => '{singular_name}',
            'menu_name'             => '{name}',
            'name_admin_bar'        => '{singular_name}',
            'add_new'               => 'Add New {singular_name}',
            'add_new_item'          => 'Add New {singular_name}',
            'new_item'              => 'New {singular_name}',
            'edit_item'             => 'Edit {singular_name}',
            'view_item'             => 'View {singular_name}',
            'all_items'             => 'All {name}',
            'search_items'          => 'Search {name}',
            'parent_item_colon'     => 'Parent {name}:',
            'not_found'             => 'No {name} found.',
            'not_found_in_trash'    => 'No {name} found in Trash.',
            'featured_image'        => '{singular_name} Cover Image',
            'set_featured_image'    => 'Set cover image',
            'remove_featured_image' => 'Remove cover image',
            'use_featured_image'    => 'Use as cover image',
            'archives'              => '{singular_name} archives',
            'insert_into_item'      => 'Insert into {singular_name}',
            'uploaded_to_this_item' => 'Uploaded to this {singular_name}',
            'filter_items_list'     => 'Filter {name} list',
            'items_list_navigation' => '{name} list navigation',
            'items_list'            => '{name} list',
        );

        foreach ($wp_labels as $key  => $label) {
            $label = str_replace('{name}', $name, $label);
            $label = str_replace('{singular_name}', $singular_name, $label);

            $wp_labels[$key] = __($label, Constant::TRANSLATION_DOMAIN);
        }

        return $wp_labels;
    }
}
