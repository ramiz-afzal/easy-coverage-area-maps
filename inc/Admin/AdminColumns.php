<?php

namespace EASY_COVERAGE_AREA_MAPS\Admin;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;

class AdminColumns
{

    public function register()
    {
        /**
         * attach hooks and filters for custom admin columns for CPTs
         */
        $custom_post_types = self::get_cpts();
        foreach ($custom_post_types as $post_type) {
            add_filter("manage_posts_columns", [$this, 'register_custom_admin_column'], 10, 2);
            add_action("manage_{$post_type}_posts_custom_column", [$this, 'render_custom_admin_column'], 10, 2);
        }
    }


    public static function get_cpts()
    {
        $custom_post_types      = array();
        $custom_post_types[]    = Constant::CPT_CUSTOM_AREA_MAPS;
        $custom_post_types[]    = Constant::CPT_CUSTOM_STATUS;
        return $custom_post_types;
    }


    /**
     * 
     */
    public function register_custom_admin_column($columns, $post_type)
    {
        if ($post_type == Constant::CPT_CUSTOM_STATUS) {
            $columns = array(
                'cb'            => $columns['cb'],
                'title'         => __('Title'),
                'status_color'  => __('Status Color'),
            );
        } else if ($post_type == Constant::CPT_CUSTOM_AREA_MAPS) {
            $columns = array(
                'cb'        => $columns['cb'],
                'title'     => __('Title'),
                'shortcode' => __('Shortcode'),
            );
        }

        return $columns;
    }


    /**
     * 
     */
    public function render_custom_admin_column($column_key, $post_id)
    {
        $post_type = get_post($post_id)->post_type;

        if ($post_type == Constant::CPT_CUSTOM_STATUS) {

            if ($column_key == 'status_color') {

                $value = 'N/A';
                $meta = carbon_get_post_meta($post_id, Functions::prefix('color'));
                if (!empty($meta)) {
                    $value = "<div style='width: 20px; padding: 10px; background: " . $meta . "';></div>";
                }
                echo $value;
            }
        } else if ($post_type == Constant::CPT_CUSTOM_AREA_MAPS) {

            if ($column_key == 'shortcode') {
                echo  '<input type="text" value=\'[ecap_map id="' . $post_id . '"]\' disabled readonly/>';
            }
        }
    }
}
