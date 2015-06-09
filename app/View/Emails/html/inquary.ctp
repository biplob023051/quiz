<h1><?php echo __('General Inquary'); ?></h1>
<p><?php echo __('Sender email address:') . ' ' . $this->request->data['email']; ?></p>
<h1><?php echo __('Message'); ?></h1>
<p><?php echo $this->request->data['message']; ?></p>