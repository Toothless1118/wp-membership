<?php $content = str_replace(array('<p>','</p>'), '', $content);  ?>

<div class="flexslider op-testimonial-slider op-testimonial-slider-style-2">
    <div class="flex-viewport" style="overflow: hidden; position: relative;">
        <ul class="op-testimonial-slides" style="width: 100%;">
                <li class="op-testimonial-slide-tesl_27decd0c33cbd16bb75c2cf17f13546c-5 flex-active-slide" style="display: block;">
                    <span class="op-star-wrap" style="background-color: <?php echo $header_color; ?>">
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                        <span class="op-star-icon"> </span>
                    </span>
                    <span class="op-testimonial-slide-text">
                        <div class="op-testimonial-slide-text-cell">
                            <blockquote><?php echo $content; ?></blockquote>
                            <span class="op-testimonial-slide-text-name"><strong><?php echo $name; ?></strong><?php echo $company; ?></span>
                        </div>
                   </span>
                </li>
        </ul>
    </div>
</div>
