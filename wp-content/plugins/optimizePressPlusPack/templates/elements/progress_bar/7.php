<div class="progressbar-style-7" id="progressbar-<?php echo $id; ?>"></div>
<script type="text/javascript">
    (function ($) {
        $(function() {
            $("#progressbar-<?php echo $id; ?>").progressbar({ value: 1 });
            $("#progressbar-<?php echo $id; ?> > .ui-progressbar-value").css("background-color", "<?php echo $color; ?>").css("color", "<?php echo $color; ?>").append('<span><?php echo $percentage . '%' . ($content ? ' ' . $content : ''); ?></span>').animate({ width: "<?php echo $percentage; ?>%"}, 500);
        });
    }(opjq));
</script>
