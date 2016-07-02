<h1><?php echo __('New User'); ?></h1>
<p><?php echo __('Id: ') . $data['User']['id']; ?></p>
<p><?php echo __('Name: ') . $data['User']['name']; ?></p>
<p><?php echo __('Email: ') . $data['User']['email']; ?></p>
<p><?php echo __('Registered: ') . date('d-m-Y', strtotime($data['User']['created'])); ?></p>
<p><?php echo $data['User']['id']; ?>;<?php echo $data['User']['name']; ?>;<?php echo $data['User']['email']; ?>;<?php echo date('Y-m-d', strtotime($data['User']['created'])); ?></p>