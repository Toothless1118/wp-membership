<div class="op-bsw-grey-panel-content op-bsw-grey-panel-no-sidebar cf">
<?php if (op_get_option('gotowebinar_api_key') === false) : ?>
	<label for="op_sections_email_marketing_services_gotowebinar_api_key" class="form-title"><?php _e('GoToWebinar API key', 'optimizepress'); ?></label>
    <p class="op-micro-copy"><?php _e('Copy GoToWebinar API key here.', 'optimizepress'); ?></p>
    <?php op_text_field('op[sections][email_marketing_services][gotowebinar_api_key]', op_get_option('gotowebinar_api_key')); ?>
<?php else : ?>
    <label for="op_sections_email_marketing_services_gotowebinar_access_token" class="form-title"><?php _e('GoToWebinar API connection', 'optimizepress'); ?></label>
    <?php if (op_get_option('gotowebinar_access_token') === false || op_get_option('gotowebinar_organizer_key') === false): ?>
    <p class="op-micro-copy">
    	<?php _e('GoToWebinar is disconnected.', 'optimizepress'); ?> <a href="<?php echo admin_url('admin.php?action=' . OP_GOTOWEBINAR_AUTH_URL); ?>&authorize=1"><?php _e('Connect', 'optimizepress'); ?></a>
    	<?php _e('or', 'optimizepress'); ?> <a href="<?php echo admin_url('admin.php?action=' . OP_GOTOWEBINAR_AUTH_URL); ?>&clean=1"><?php _e('Clean API key', 'optimizepress'); ?></a>
    </p>
	<?php else: ?>
	<p class="op-micro-copy"><?php _e('GoToWebinar is connected.', 'optimizepress'); ?> <a href="<?php echo admin_url('admin.php?action=' . OP_GOTOWEBINAR_AUTH_URL); ?>&disconnect=1"><?php _e('Disconnect', 'optimizepress'); ?></a></p>
	<?php endif; ?>
<?php endif; ?>
</div>