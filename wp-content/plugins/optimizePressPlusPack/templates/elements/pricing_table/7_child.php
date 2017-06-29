<div class="pricing-table-column<?php echo (!empty($most_popular_text) && $most_popular=='Y' ? ' popular' : '') . ((( ! empty($feature_description)) or ( ! empty($items))) ? ' has-features' : ''); ?>">
    <div class="pricing-table-column-content">
        <div class="price-table">
            <div class="name"><?php echo $title; ?></div>
            <div class="price"><span class="unit"><?php echo $pricing_unit; ?></span><?php echo $price; ?><?php echo (!empty($pricing_variable) ? '<span class="variable">'.$pricing_variable.'</span>' : ''); ?></div>
            <div class="pricing-description"><?php echo $pricing_description; ?></div>
            <div class="description"><?php echo wpautop($package_description); ?></div>
        </div>
        <div class="feature-table">
            <h3 class="feature-description"><?php echo $feature_description; ?></h3>
            <ul class="features"><?php echo $items; ?></ul>
        </div>
        <div class="submit-table">
            <div class="popular"><?php echo $most_popular_text; ?></div>
            <a href="<?php echo $order_button_url; ?>" class="button"><?php echo $order_button_text; ?></a>
        </div>
    </div>
</div>
