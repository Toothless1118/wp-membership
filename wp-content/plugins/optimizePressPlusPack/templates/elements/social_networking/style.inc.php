<style>
    <?php
    if ($style == "style-1") {
        echo '#'.$id.'{ background-color: ' .  $data['background_color'] . ' !important; }';
    }

    if ($style == "style-2" && $data['icon_and_font_color_box'] != '') {
        echo '#'.$id.' .logo { fill: ' .  $data['icon_and_font_color_box'] . ' !important; }';
        echo '#'.$id.' .logo .you, #'.$id.' .logo .tube { fill: ' .  $data['icon_and_font_color_box'] . ' !important; }';
        echo '#'.$id.' p { color: ' .  $data['icon_and_font_color_box'] . ' !important; }';
    }

   if ($style == "style-3") {
        echo '#'.$id.' .box { background-color: ' .  $data['background_color'] . '; }';
        echo '#'.$id.' .logo { fill: ' . $data['icon_and_font_color_box'] . '; }';
   }
  ?>
</style>