<div class="ecap_shortcode_error">
    <?php if (isset($message)): ?>
        <p>Error: <?= $message; ?></p>
    <?php else: ?>
        <p>An error occurred while trying to render Shortcode</p>
    <?php endif; ?>
</div>