<?php
	$now = new DateTime();
	$future_date = new DateTime($setting['maintenance_time']);
	$interval = $future_date->diff($now);
	$till = $interval->format("%a days, %h hours, %i minutes");
	$datetime_replace = str_replace('{datetime}', $setting['maintenance_time'], $setting['alert_message']);
	$message = str_replace('{time}', $till, $datetime_replace);
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger">
				<a class="close" data-dismiss="alert" href="#">&times;</a>
				<i class="fa fa-warning"></i>
				<?php echo h($message); ?>
			</div>
		</div>
	</div>
</div>