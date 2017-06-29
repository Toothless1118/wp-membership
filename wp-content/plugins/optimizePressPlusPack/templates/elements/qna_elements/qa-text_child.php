<?php $content = preg_replace('{<p[^>]*>}i','<p>'.'<span>'.__('A.', 'optimizepress-plus-pack').'</span>',$content,1); ?>
<li>
    <h3><span><?php echo __('Q.', 'optimizepress-plus-pack'); ?></span><?php echo $question;?></h3>
    <?php echo $content;?>
</li>