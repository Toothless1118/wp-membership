<?php $content = str_replace(array('<p>','</p>'), '', $content);  ?>

<div class="flexslider op-testimonial-slider op-testimonial-slider-style-3" style="background-color:#F2F9FF;">
    <div class="flex-viewport" style="overflow: hidden; position: relative;">
        <ul class="op-testimonial-slides" style="width: 100%; transition-duration: 0s; transform: translate3d(0px, 0px, 0px);">
        <li class="flex-active-slide" style="float: left; display: block;">
            <img src="<?php echo $image; ?>" class="op-testimonial-slide-photo" width="91" height="91" title="" alt="Name" draggable="false">
            <span class="ts-right">
                <blockquote><?php echo $content; ?></blockquote>
                <span class="op-testimonial-slide-name"><strong><?php echo $name; ?></strong> <?php echo $company; ?> </span>
            </span>
        </li>
    </div>
</div>
