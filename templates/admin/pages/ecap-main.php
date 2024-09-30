<?php
defined('ABSPATH') or die();
?>
<h1><?= get_admin_page_title(); ?></h1>
<div class="wrap">
    <section>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>Shortcode</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody class="the-list">
                <tr>
                    <td><code>[ecap_map id="12345"]</code></td>
                    <td>Displays a defined service area map on the frontend</td>
                </tr>
            </tbody>
        </table>
    </section>
</div>