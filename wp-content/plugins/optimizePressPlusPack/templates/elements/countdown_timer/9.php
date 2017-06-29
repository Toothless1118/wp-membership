<?php require_once('style.inc.php'); ?>
<?php load_countdown_font('montserrat'); ?>

<div id="<?php echo $id; ?>" class="countdown-timer countdown-timer-style-9"<?php echo (!empty($end_date) ? ' data-end="'.$end_date.'"' : '') ?><?php echo (!empty($redirect_url) ? ' data-redirect_url="'.$redirect_url.'"' : '') ?> data-years_text_singular="<?php echo $years_text_singular; ?>" data-years_text="<?php echo $years_text; ?>" data-months_text_singular="<?php echo $months_text_singular; ?>" data-months_text="<?php echo $months_text; ?>" data-weeks_text_singular="<?php echo $weeks_text_singular; ?>" data-weeks_text="<?php echo $weeks_text; ?>" data-days_text_singular="<?php echo $days_text_singular; ?>" data-days_text="<?php echo $days_text; ?>" data-hours_text_singular="<?php echo $hours_text_singular; ?>" data-hours_text="<?php echo $hours_text; ?>" data-minutes_text_singular="<?php echo $minutes_text_singular; ?>" data-minutes_text="<?php echo $minutes_text; ?>" data-seconds_text_singular="<?php echo $seconds_text_singular; ?>" data-seconds_text="<?php echo $seconds_text; ?>" data-format="dhms">
    <div id="countdownTimer"></div>
</div>
