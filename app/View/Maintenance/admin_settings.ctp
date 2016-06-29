<?php
	$this->Html->css(array('bootstrap-datetimepicker.min'), array('inline' => false));
    $this->Html->script(array('moment', 'bootstrap-datetimepicker.min', 'settings'), array('inline' => false));
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
    	<?php echo $this->Session->flash('success'); ?>
    	<?php echo $this->Session->flash('error'); ?>
        <form action="<?php echo $this->request->base; ?>/admin/maintenance/settings" enctype="multipart/form-data" method="post" id="settings">
	        <h4><?php echo __('Alert Section'); ?></h4>
	    	<hr>
			<div class="form-group">
				<label>Offline Alert Message - {datetime} wildcard will be replaced by actual Maintenance date time and {time} will be replace by time left of maintenace</label>
				<?php echo $this->Form->text('alert_message', array('class' => 'form-control', 'value' => $setting['alert_message'])); ?>
			</div>
			<div class="form-group">
				<label>Maintenance Date Time</label>
				<?php echo $this->Form->text('maintenance_time', array('class' => 'form-control datepicker', 'value' => $setting['maintenance_time'])); ?>
			</div>
			<div class="form-group">
				<label>Make It Visible</label>
				<?php
					if ($setting['visible']) {
						echo $this->Form->text('visible', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
					}
					else {
						echo $this->Form->text('visible', array('type' => 'checkbox', 'class' => ''));
					}
				?>
			</div>
			<h4><?php echo __('Offline Section'); ?></h4>
	    	<hr>
			<div class="form-group">
				<label>Offline Message</label>
				<?php echo $this->Form->text('offline_message', array('class' => 'form-control', 'value' => $setting['offline_message'])); ?>
			</div>
			<div class="form-group">
				<label>Make Offline</label>
				<?php
					if ($setting['offline_status']) {
						echo $this->Form->text('offline_status', array('type' => 'checkbox', 'checked' => true, 'class' => '')); 
					}
					else {
						echo $this->Form->text('offline_status', array('type' => 'checkbox', 'class' => ''));
					}
				?>
			</div>
			<div class="regSubmit">
				<input type="submit" value="Save Settings" class="btn btn-primary btn-xlarge">
			</div>
		</form>
    </div>
</div>