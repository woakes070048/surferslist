<?php if (count($currencies) > 1) { ?>
<div id="hboxcurrency" class="hbox has-menu">
    <div class="heading">
        <span class="span-title mini-info"><i class="fa fa-money" title="<?php echo $text_currency; ?>"></i><?php echo $currency_code; ?></span>
    </div>
    <div class="content">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        <ul id="currency" class="list">
        	<?php foreach ($currencies as $currency) { ?>
            <?php if ($currency['code'] == $currency_code) { ?>
            <?php if ($currency['symbol_left']) { ?>
            <li><span class="selected"><?php echo $currency['title']; ?> (<?php echo $currency['symbol_left']; ?>)</span></li>
            <?php } else { ?>
            <li><span class="selected"><?php echo $currency['title']; ?> (<?php echo $currency['symbol_right']; ?>)</span></li>
            <?php } ?>
            <?php } else { ?>
            <?php if ($currency['symbol_left']) { ?>
            <li><a data-code="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?> (<?php echo $currency['symbol_left']; ?>)</a></li>
            <?php } else { ?>
            <li><a data-code="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?> (<?php echo $currency['symbol_right']; ?>)</a></li>
            <?php } ?>
            <?php } ?>
            <?php } ?>
        </ul>
        <input type="hidden" name="currency_code" value="" />
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        </form>
    </div>
</div>
<?php } ?>
