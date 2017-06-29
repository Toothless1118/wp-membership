<?php $content = str_replace(array('<p>','</p>'), '', $content);  ?>

<div class="flexslider op-testimonial-slider op-testimonial-slider-style-5">
    <div class="flex-viewport" style="overflow: hidden; position: relative;">
        <ul class="op-testimonial-slides op-testimonial-slides-1-columns" style="width: 100%;">
            <li class="op-testimonial-slide-tesl_c41aed6be537f8d4cebc21b5e5146c98-2" style="float: left; display: block;">
                <div class="op-testimonial-slide-photo-wrap">
                    <img src="<?php echo $image; ?>" class="op-testimonial-slide-photo" width="100" height="100" title="" alt="Name" draggable="false">
                </div>
                <blockquote>
                    <strong><?php echo $name; ?></strong> - <span><?php echo $content; ?></span>
                </blockquote>
            </li>
        </ul>
    </div>
</div>
