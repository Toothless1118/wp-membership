<?php $content = str_replace(array('<p>','</p>'), '', $content);  ?>

<div class="flexslider op-testimonial-slider op-testimonial-slider-style-1">
    <ul class="op-testimonial-slides">
        <div class="op-testimonial-slider-photo-wrap">
            <img src="<?php echo $image; ?>" class="op-testimonial-slider-photo" width="110" height="110" title="" alt="Name">
            <span class="op-testimonial-slider-name"><strong><?php echo $name; ?></strong> <?php echo $company?></span>
        </div>
        <span class="op-testimonial-slider-right">
            <blockquote><?php echo $content; ?></blockquote><a href="<?php echo $href; ?>" class="op-btn-cta"><?php echo $button_text; ?></a>
        </span>
    </ul>
</div>
