<?php
    include('style.inc.php');
    $pinterestCount = json_decode(substr(file_get_contents("http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=" . urlencode($p_url)), 13,-1), true);
?>
<ul id="<?php echo $id; ?>" id="<?php echo $id; ?>" data-counter="true" class="social-sharing social-sharing-style-25">
    <li class="facebook" data-likes="<?php echo OptimizePress_Social_Count::getInstance()->getFacebookShareCount($fb_like_url); ?>" data-lang="<?php echo $fb_lang; ?>" data-url="<?php echo $fb_like_url; ?>"<?php echo (!empty($fb_button_text) ? ' data-title="'.$fb_button_text.'"' : ''); ?><?php echo (!empty($fb_text) ? ' data-text="'.$fb_text.'"' : ''); ?> data-colorscheme="<?php echo $fb_color; ?>"></li>
    <li class="twitter" data-likes="<?php echo OptimizePress_Social_Count::getInstance()->getTwitterShareCount($tw_url); ?>" data-lang="<?php echo $tw_lang; ?>" data-url="<?php echo $tw_url; ?>"<?php echo (!empty($tw_button_text) ? ' data-title="'.$tw_button_text.'"' : ''); ?><?php echo (!empty($tw_button_text) ? ' data-text="'.$tw_button_text.'"' : ''); ?> data-colorscheme="<?php echo $tw_color; ?>"></li>
    <li class="googlePlus" data-lang="<?php echo $g_lang; ?>" data-url="<?php echo $g_url; ?>"<?php echo (!empty($g_button_text) ? ' data-title="'.$g_button_text.'"' : ''); ?><?php echo (!empty($g_button_text) ? ' data-text="'.$g_button_text.'"' : ''); ?> data-colorscheme="<?php echo $g_color; ?>"></li>
    <li class="linkedin" data-lang="<?php echo $linkedin_lang; ?>" data-url="<?php echo $linkedin_url; ?>"<?php echo (!empty($linkedin_button_text) ? ' data-title="'.$linkedin_button_text.'"' : ''); ?><?php echo (!empty($linkedin_button_text) ? ' data-text="'.$linkedin_button_text.'"' : ''); ?> data-colorscheme="<?php echo $linkedin_color; ?>"></li>
    <li class="pinterest" data-colorscheme="<?php echo $linkedin_color; ?>">
        <a href="http://pinterest.com/pin/create/link/?url=<?php echo urlencode($p_url); ?>&media=<?php echo urlencode($p_image_url); ?>&description=<?php echo urlencode($p_description); ?>" target="_blank">
            <div class="box">
                <span class="count"><?php echo $pinterestCount["count"]; ?></span>
            </div>
        </a>
    </li>
</ul>

<script>
if (typeof opjq === 'function') {
    opjq(window).trigger('op_init_sharrre');
}
</script>
