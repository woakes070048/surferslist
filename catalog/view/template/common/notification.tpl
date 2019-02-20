<?php if ($notification || $success || $error_warning) { ?>
<div class="widget notification">
	<span class="close"><i class="fa fa-times-circle-o"></i></span>

	<h6><?php echo $text_notification; ?></h6>

	<?php if ($success) { ?>
	<div class="success">
		<p><?php echo $success; ?></p>
		<span class="icon"><i class="fa fa-check"></i></span>
	</div>
	<?php } ?>

	<?php if ($error_warning) { ?>
	<div class="warning">
		<p><?php echo $error_warning; ?></p>
		<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
	</div>
	<?php } ?>

	<?php if ($notification) { ?>
		<div class="attention">
			<p><?php echo $notification; ?></p>
			<span class="icon"><i class="fa fa-info-circle"></i></span>
		</div>
	<?php } ?>

</div>
<?php } ?>
