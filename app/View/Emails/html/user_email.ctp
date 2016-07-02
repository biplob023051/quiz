<?php $code = $data['User']['id'] . 'y-s' . $data['User']['activation']; ?>
<h1><?php echo __('Welcome to Verkkotesti'); ?></h1>
<p><?php echo __('Hi') . ' ' . $data['User']['name'] . ','; ?></p>
<p><?php echo __('Thanks for registering with us. To activate your account, please click bellow link or simply copy paste the url to your browser.'); ?></p>
<p><a href="<?php echo Router::url(array('controller'=>'user', 'action' => 'confirmation', $code),true); ?>"><?php echo Router::url(array('controller'=>'user', 'action' => 'confirmation', $code),true); ?></a></p>
<small><?php echo __('If you don\'t register on Verkkotesti.com, simply ignore this email.'); ?></small>