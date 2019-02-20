<div class="widget">
	<h6><?php echo $heading_title; ?></h6>
    <p style="padding:0 20px 40px 20px;"><?php echo $text_store; ?></p>
    <ul class="widget-list">
    	<?php foreach ($stores as $store) { ?>
        <?php if ($store['store_id'] == $store_id) { ?>
        <li><a class="a_icon" href="<?php echo $store['url']; ?>"><?php echo $store['name']; ?><i class="fa fa-check icon_list"></i></a></li>
        <?php } else { ?>
        <li><a href="<?php echo $store['url']; ?>"><?php echo $store['name']; ?></a></li>
        <?php } ?>
        <?php } ?>
    </ul>
</div>