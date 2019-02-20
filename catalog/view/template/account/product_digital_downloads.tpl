<?php if ($error) { ?>
<div class="warning">
	<p><?php echo $error; ?></p>
	<span class="close"><i class="fa fa-times"></i></span>
	<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
</div>
<?php } ?>
<?php if ($success) { ?>
<div class="success">
	<p><?php echo $success; ?></p>
	<span class="close"><i class="fa fa-times"></i></span>
	<span class="icon"><i class="fa fa-success-sign"></i></span>
</div>
<?php } ?>
<table class="form">
	<tr>
	  <td><span class="required">*</span> <?php echo $entry_download; ?></td>
	  <td><div class="scrollbox">
		  <?php $class = 'odd'; ?>
		  <?php foreach ($digital_downloads as $digital_download) { ?>
		  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
		  <div class="<?php echo $class; ?>">
			<?php if (in_array($digital_download['download_id'], $product_download)) { ?>
			<input type="checkbox" name="product_download[]" value="<?php echo $digital_download['download_id']; ?>" checked="checked" />
			<?php echo $digital_download['name']; ?>
			<?php } else { ?>
			<input type="checkbox" name="product_download[]" value="<?php echo $digital_download['download_id']; ?>" />
			<?php echo $digital_download['name']; ?>
			<?php } ?>
		  </div>
		  <?php } ?>
		</div>
	  </td>
	</tr>
</table>