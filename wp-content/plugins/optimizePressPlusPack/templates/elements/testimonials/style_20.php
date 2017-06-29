<?php $content = str_replace(array('<p>','</p>'), '', $content);  ?>
<div class="flexslider op-testimonial-slider op-testimonial-slider-style-4">
    <div class="flex-viewport" style="overflow: hidden; position: relative;">
        <ul class="op-testimonial-slides op-testimonial-slides-1-columns" style="width: 100%;">
            <li style="float: left; display: block;">
                <blockquote><?php echo $content; ?></blockquote>
                <div class="op-testimonial-slide-photo-wrap">
                    <img src="<?php echo $image; ?>" class="op-testimonial-slide-photo" width="110" height="110" title="" alt="Name" draggable="false">
                    <span class="op-testimonial-slide-name">
                        <strong><?php echo $name; ?></strong>
                        <?php echo ' ' . $company; ?>
                    </span>
                </div>
            </li>
        </ul>
    </div>
</div>
