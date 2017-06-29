<div class="alignleft">
  <a href="<?php echo admin_url('admin-ajax.php?action=' . $action . '&' . $_SERVER['QUERY_STRING']); ?>"><?php printf(__('Export table as CSV (%d records)', 'memberpress'), $itemcount); ?></a>
  <?php MeprHooks::do_action('mepr-control-table-footer', $action, $totalitems); ?>
</div>
