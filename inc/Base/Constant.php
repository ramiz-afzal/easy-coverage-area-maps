<?php

namespace EASY_COVERAGE_AREA_MAPS\Base;

if (!defined('ABSPATH')) exit;

final class Constant
{
    // miscellaneous
    const DATE_FORMAT           = 'Y-m-d H:i:s';
    const TRANSLATION_DOMAIN    = 'easy-coverage-area-maps';

    // URLs & Slugs
    const SLUG_ADMIN_MENU = 'ecap-settings';

    // custom post types
    const CPT_CUSTOM_AREA_MAPS  = 'ecap-coverage-map';

    // custom meta boxes
    const META_BOX_MAP_PREVIEW  = 'ecap-map-preview';
    const META_BOX_MAP_REGIONS  = 'ecap-map-regions';
}
