<h1><?php echo __('New User'); ?></h1>
<p><?php echo __('Id: ') . $user['User']['id']; ?></p>
<p><?php echo __('Name: ') . $user['User']['name']; ?></p>
<p><?php echo __('Email: ') . $user['User']['email']; ?></p>
<p><?php echo __('Registered: ') . date('d-m-Y', strtotime($user['User']['created'])); ?></p>
<p><?php echo $user['User']['id']; ?>;<?php echo $user['User']['name']; ?>;<?php echo $user['User']['email']; ?>;<?php echo date('Y-m-d', strtotime($user['User']['created'])); ?></p>