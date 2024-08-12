<?php

namespace EASY_COVERAGE_AREA_MAPS\Admin;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;

class CustomMetaBoxes
{
    public function register()
    {
        // add custom metabox
        add_action('add_meta_boxes', [$this, 'add_meta_boxes'], 10, 1);
    }


    /**
     * 
     */
    public static function get_meta_boxes($object_context = null)
    {
        return array(
            Constant::CPT_CUSTOM_AREA_MAPS => array(
                array(
                    'id'            => Constant::META_BOX_MAP_REGIONS,
                    'title'         => 'Regions',
                    'callback'      => [$object_context, 'render_custom_metabox'],
                    'screen'        => Constant::CPT_CUSTOM_AREA_MAPS,
                    'context'       => 'normal',
                    'priority'      => 'high',
                    'callback_args' => null,
                ),
                array(
                    'id'            => Constant::META_BOX_MAP_PREVIEW,
                    'title'         => 'Preview',
                    'callback'      => [$object_context, 'render_custom_metabox'],
                    'screen'        => Constant::CPT_CUSTOM_AREA_MAPS,
                    'context'       => 'normal',
                    'priority'      => 'default',
                    'callback_args' => null,
                ),
            ),
        );
    }


    public function add_meta_boxes($current_post_type)
    {
        $custom_meta_boxes = self::get_meta_boxes($this);
        if (empty($custom_meta_boxes)) {
            return;
        }

        $post_type_meta_boxes = isset($custom_meta_boxes[$current_post_type]) ? $custom_meta_boxes[$current_post_type] : null;
        if (empty($post_type_meta_boxes)) {
            return;
        }

        foreach ($post_type_meta_boxes as $meta_box) {
            $id             = isset($meta_box['id']) ? $meta_box['id'] : uniqid('tpg_', true);
            $title          = isset($meta_box['title']) ? $meta_box['title'] : NULL;
            $callback       = isset($meta_box['callback']) ? $meta_box['callback'] : NULL;
            $screen         = isset($meta_box['screen']) ? $meta_box['screen'] : $current_post_type;
            $context        = isset($meta_box['context']) ? $meta_box['context'] : NULL;
            $priority       = isset($meta_box['priority']) ? $meta_box['priority'] : NULL;
            $callback_args  = isset($meta_box['callback_args']) ? $meta_box['callback_args'] : [];

            add_meta_box($id, $title, $callback, $screen, $context, $priority, $callback_args);
        }
    }


    public function render_custom_metabox($post = null, $args = null)
    {
        if (empty($post)) {
            return;
        }

        $meta_box_id = $args && isset($args['id']) ? $args['id'] : null;
        if (empty($meta_box_id)) {
            return;
        }

        Functions::get_template("admin/metaboxes/{$meta_box_id}.php", ['post_id' => $post->ID, 'args' => $args], true);
    }
}
