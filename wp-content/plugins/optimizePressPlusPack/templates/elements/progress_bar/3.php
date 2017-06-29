<div class="progressbar-style-3" id="progressbar-<?php echo $id; ?>">
    <span><?php echo $percentage . '%' . ($content ? ' ' . $content : ''); ?></span>
</div>
<script type="text/javascript">
    (function ($) {
        $(function() {
            $("#progressbar-<?php echo $id; ?>").progressbar({ value: 1 });
            $("#progressbar-<?php echo $id; ?> > .ui-progressbar-value").css("background-color", "<?php echo $color; ?>").animate({ width: "<?php echo $percentage; ?>%"}, 500);
        });
    }(opjq));
</script>
