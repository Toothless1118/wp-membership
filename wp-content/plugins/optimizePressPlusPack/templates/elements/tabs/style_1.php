<div id="tab-<?php echo $id; ?>" class="tab-element tabbed-panel tab-style-1<?php if ($color_select) echo ' tab-color-' . $color_select; ?>">
	<ul class="tabs cf"><?php echo $tabs_html; ?></ul>
	<div class="clearfix"></div>
	<div class="tab-content-container">
<?php echo $content; ?>
	</div>
</div>
<script>
	opjq(document).ready(function(e) {
		opjq( "#tab-<?php echo $id; ?>" ).tabs({
			hide: { effect: "fade", duration: 100 },
		});
	});
</script>
<script type="text/javascript">
	;(function($){
		$('.tabbed-panel .tabs li a').click(function(e){
			var li = $(this).parent(), ul = li.parent(), idx = ul.find('li').index(li), panel = ul.parent();
			panel.find('> .tab-content-container').find('> .tab-content').hide().end().find('> .tab-content:eq('+idx+')').show();
			ul.find('.selected').removeClass('selected');
			li.addClass('selected');
			e.preventDefault();
		});$('.tabbed-panel .tabs li:first-child a').click();
	})(opjq);
</script>

