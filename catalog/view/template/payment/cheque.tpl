<div class="content">
    <h2><?php echo $text_instruction; ?></h2>

    <h3><?php echo $text_payable; ?></h3>
    <p><?php echo $payable; ?></p>

    <h3><?php echo $text_address; ?></h3>
    <p><?php echo $address; ?></p>

    <h3><?php echo $text_memo; ?></h3>
    <p><?php echo $memo; ?></p>

    <div class="attention">
        <p><?php echo $text_payment; ?></p>
        <span class="icon"><i class="fa fa-info-circle"></i></span>
    </div>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm-cheque" data-success="<?php echo $continue; ?>" class="button button_highlight bigger" />
  </div>
</div>
