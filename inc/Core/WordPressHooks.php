<?php

namespace EASY_COVERAGE_AREA_MAPS\Core;

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
use EASY_COVERAGE_AREA_MAPS\Base\Constant;

class WordPressHooks
{
    public function register()
    {
        // register .json file types
        add_filter('upload_mimes', [$this, 'register_custom_mime_types']);
    }

    /**
     * @return WP_Post[] $statuses
     */
    public static function get_statuses()
    {
        try {
            $args = array(
                'post_type'     => Constant::CPT_CUSTOM_STATUS,
                'post_status'   => 'publish',
                'numberposts'   => -1,
            );
            $posts = get_posts($args);

            if (empty($posts)) {
                return [];
            }

            foreach ($posts as &$post) {
                $post->title    = $post->post_title;
                $post->desc     = carbon_get_post_meta($post->ID, Functions::prefix('desc'));
                $post->color    = carbon_get_post_meta($post->ID, Functions::prefix('color'));
            }

            return $posts;
        } catch (\Throwable $error) {
            Functions::debug_log("Error occurred: " . $error->getMessage());
            return [];
        }
    }

    /**
     * @return array $statuses
     */
    public static function get_statuses_as_options()
    {
        try {
            $posts = self::get_statuses();

            if (empty($posts)) {
                return [];
            }

            $posts      = json_decode(json_encode($posts), true);
            $post_ids   = array_column($posts, 'ID');
            $post_title = array_column($posts, 'post_title');

            return array_combine($post_ids, $post_title);
        } catch (\Throwable $error) {
            Functions::debug_log("Error occurred: " . $error->getMessage());
            return [];
        }
    }


    /**
     * @param array $mimes
     * @return array $mimes
     */
    public function register_custom_mime_types($mimes)
    {
        try {
            return array_merge($mimes, ['json' => 'text/plain', 'geojson' => 'text/plain']);
        } catch (\Throwable $error) {
            Functions::debug_log("Error occurred: " . $error->getMessage());
            return $mimes;
        }
    }
}
