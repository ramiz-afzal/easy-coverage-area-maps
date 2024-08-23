<?php

namespace EASY_COVERAGE_AREA_MAPS\Base;

if (!defined('ABSPATH')) exit;

final class Constant
{
    // miscellaneous
    const DATE_FORMAT           = 'Y-m-d H:i:s';
    const TRANSLATION_DOMAIN    = 'easy-coverage-area-maps';

    // URLs & Slugs
    const SLUG_ADMIN_MENU           = 'ecap-main';
    const SLUG_ADMIN_MENU_SETTINGS  = 'ecap-settings';
    const SLUG_ADMIN_MENU_RADAR     = 'ecap-radar';

    // custom post types
    const CPT_CUSTOM_AREA_MAPS  = 'ecap-coverage-map';
    const CPT_CUSTOM_STATUS     = 'ecap-coverage-status';

    // custom taxonomies
    const TAXONOMY_PRODUCT_BRAND = 'ecap_';

    // API endpoints
    const URL_API_LIVE = 'https://api.radar.io/v1';
}
