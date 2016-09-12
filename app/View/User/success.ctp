<?php //echo $this->Session->flash('success'); ?>
<div class="row">
	<div class="col-md-12">
		<?php 
			echo __('Thank you for creating a user account! We\'ve sent a confirmation email to your given email address. Check your email and click the link in it to activate your account. If you need help, take contact') . ' ';
			echo $this->Html->link('here', array('controller' => 'pages', 'action' => 'display', 'contact')); 
		?>
	</div>
</div>