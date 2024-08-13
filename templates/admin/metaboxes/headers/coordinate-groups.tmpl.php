<?php

use EASY_COVERAGE_AREA_MAPS\Base\Functions;
?>
<% if (<?= Functions::prefix('lat'); ?> && <?= Functions::prefix('long'); ?>) { %>
Lat: <%- <?= Functions::prefix('lat'); ?> %>, Long: <%- <?= Functions::prefix('long'); ?> %>
<% } %>