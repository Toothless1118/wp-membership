<div class="pt-border <?php echo (!empty($most_popular_text) && $most_popular=='Y' ? 'popular' : ''); ?>">
    <div class="pricing-table-col">
        <div class="price-table" mp_content="<?php echo $most_popular_text; ?>">
            <div class="name"><?php echo $title; ?></div>
            <div class="price"><span class="unit"><?php echo $pricing_unit; ?></span><?php echo $price; ?><?php echo (!empty($pricing_variable) ? '<span class="variable">'.$pricing_variable.'</span>' : ''); ?></div>
            <div class="pricing-description"><?php echo $pricing_description; ?></div>
            <ul class="features"><?php echo $items; ?></ul>
            <a href="<?php echo $order_button_url; ?>" class="css-button"><?php echo $order_button_text; ?></a>
            <div class="description"><?php echo wpautop($package_description); ?></div>
        </div>
    </div>
</div>
