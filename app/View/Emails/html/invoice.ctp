<h1><?php echo __('Invoice'); ?></h1>
<p>User Id: <?php echo $User['id']; ?></p>
<p>User Name: <?php echo $User['name']; ?></p>
<p>User Email: <?php echo $User['email']; ?></p>
<?php if (isset($package)) : ?>
	<p>User Requested Package: <?php echo $package; ?></p>
<?php endif; ?>