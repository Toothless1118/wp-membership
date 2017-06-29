<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<?php if(isset($errors) && $errors != null && count($errors) > 0): ?>
  <?php if($product->signup_form_style == "style2"){?>

<div class="mp_wrapper">
  <div class="mepr_error" id="mepr_errors1">
    <ul>
      <?php foreach($errors as $error): ?>
        <li><strong><?php _ex('ERROR', 'ui', 'memberpress'); ?></strong>: <?php print $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php } else if ($product->signup_form_style == "style3") {?>
<div class="mp_wrapper">
  <div class="mepr_error" id="mepr_errors2">
    <ul>
      <?php foreach($errors as $error): ?>
        <li><strong><?php _ex('ERROR', 'ui', 'memberpress'); ?></strong>: <?php print $error; ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div> 
<?php } else {?>
<div class="mepr_error modal fade bd-example-modal-lg" id="mepr_errors" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Error</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <ul>
              <?php foreach($errors as $error): ?>
                <li><strong><?php _ex('ERROR', 'ui', 'memberpress'); ?></strong>: <?php print $error; ?></li>
              <?php endforeach; ?>
            </ul>
        </div>
    </div>
  </div>
</div>
<?php }?>
<?php endif; ?>

<?php if( isset($message) and !empty($message) ): ?>
<div class="mp_wrapper">
  <div class="mepr_updated"><?php echo $message; ?></div>
</div>
<?php endif; ?>
