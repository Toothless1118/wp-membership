<?php include 'style.inc.php'; ?>

<div id="<?php echo $id; ?>" class="optin-box optin-box-29"<?php echo $style_str; ?>>
    <div class="optin-box-content">
        <?php echo $form_open.$hidden_str ?>
            <div class="text-boxes">
                <?php op_get_var_e($fields,'email_field'); ?>
            </div>
            <?php echo $submit_button ?>
            <?php do_action('op_after_optin_submit_button'); ?>
        </form>
    </div>
</div>
