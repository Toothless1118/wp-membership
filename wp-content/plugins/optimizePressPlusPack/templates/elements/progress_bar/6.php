<div class="progressbar-style-6" id="progressbar-<?php echo $id; ?>"></div>
<script type="text/javascript">
    (function ($) {
        $(function() {
            $("#progressbar-<?php echo $id; ?>").css("color", "<?php echo $color; ?>").progressbar({ value: 1 });
            $("#progressbar-<?php echo $id; ?> > .ui-progressbar-value").css("background-color", "<?php echo $color; ?>").before('<span><?php echo $percentage . '%' . ($content ? ' ' . $content : ''); ?></span>').animate({ width: "<?php echo $percentage; ?>%"}, 500);
        });
    }(opjq));
</script>
