<?php
    if ($most_popular === 'Y') {
        $popular_class = ' op-comparison-table-column-popular';
    } else {
        $popular_class = '';
    }
?><div class="op-comparison-table-column<?php echo $popular_class; ?>">
    <div class="op-comparison-table-header">
        <h3 class="op-comparison-table-title"><?php echo $title; ?></h3>
        <span class="op-comparison-table-price"><?php echo $pricing_unit . $price; ?></span>
        <span class="op-comparison-table-variable"><?php echo $pricing_variable; ?></span>
        <p class="op-comparison-table-description"><?php echo $pricing_description; ?></p>
        <?php if (!empty($order_button_text) && !empty($order_button_url)): ?>
            <a class="op-comparison-table-btn" href="<?php echo $order_button_url; ?>"><?php echo $order_button_text; ?></a>
        <?php endif; ?>
        <div class="op-comparison-table-package-description"><?php echo $package_description; ?></div>
    </div><ul class="op-comparison-table-features"><?php echo $items; ?></ul>
    <?php if (!empty($order_button_text) && !empty($order_button_url)): ?>
        <div class="op-comparison-table-btn-container">
            <div class="op-comparison-table-feature-cell">
                <a class="op-comparison-table-btn" href="<?php echo $order_button_url; ?>"><?php echo $order_button_text; ?></a>
            </div>
        </div>
    <?php endif; ?>
</div>