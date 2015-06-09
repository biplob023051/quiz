<h1><?php echo __('Password Reset'); ?></h1>
<p><?php echo __('To reset your password click on the bellow link or paste on your browser.'); ?></p>
<?php echo $loginUrl . 'user/reset_password/' . $reset_code; ?>
<p><?php echo __('If you have not sent this request, just ignore this mail.'); ?></p>