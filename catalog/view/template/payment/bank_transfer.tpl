<div class="content">
    <h2><?php echo $text_instruction; ?></h2>
  <p><?php echo $text_description; ?></p>
  <p><?php echo $bank; ?></p>
  <p><?php echo $text_payment; ?></p>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript"><!--
$('.buttons').on('click', '#button-confirm', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=payment/bank_transfer/confirm',
		success: function() {
			location = '<?php echo $continue; ?>';
		}
	});
});
//--></script>
