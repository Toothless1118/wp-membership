<?php include 'style.inc.php'; ?>

<div id="<?php echo $id; ?>" class="optin-box optin-box-27"<?php echo $style_str; ?>>
    <div class="optin-box-content">
        <?php
            $headline = op_get_var($content,'headline','','<h2>%1$s</h2>');
            echo !empty($headline) ? $headline : '';

            $paragraph = op_get_var($content,'paragraph','');
            echo !empty($paragraph) ? str_replace('<p>', '<p class="description">', $paragraph) : '';

            echo $form_open.$hidden_str;
            if (isset($order)) {
                foreach ($order as $field) {
                    if ($field === 'email_field') {
                        op_get_var_e($fields,'email_field');
                    } else if ($field === 'name_field') {
                        op_get_var_e($fields,'name_field');
                    } else if (isset($extra_fields[$field])) {
                        op_get_var_e($extra_fields, $field);
                    }
                }
            } else {
                op_get_var_e($fields,'name_field');
                op_get_var_e($fields,'email_field');
                echo implode('',$extra_fields);
            }
            echo $submit_button;
            do_action('op_after_optin_submit_button');
        ?>
        </form>
        <?php
        $privacyImage = '<img src="'.OPPP_BASE_URL.'images/elements/optin_box/optin-27-privacy.png" alt="' . __('privacy','optimizepress-plus-pack') . '" width="8" height="10" /> ';
        op_get_var_e($content,'privacy','','<p class="privacy">' . $privacyImage . '%1$s</p>');
        ?>
    </div>
</div>
