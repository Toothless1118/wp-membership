<div class="tab-wrap">
	<div id="tab-<?php echo $id; ?>" class="tab-element tab-style-3<?php if ($color_select) echo ' tab-color-' . $color_select; ?>">
		<ul><?php echo $tabs_html; ?></ul>
		<div class="clearfix"></div>
		<div class="tab-content-container">
<?php echo $content; ?>
		</div>
	</div>
</div>
<script>
	opjq(document).ready(function(e) {
		opjq( "#tab-<?php echo $id; ?>" ).tabs({
			hide: { effect: "fade", duration: 100 },
		});
	});
</script>
