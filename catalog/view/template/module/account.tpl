<div class="widget">
	<h6><?php echo $heading_title; ?></h6>
    <ul class="widget-list">
        <?php if (!$logged) { ?>
        <li><a class="a_icon" href="<?php echo $login; ?>"><?php echo $text_login; ?><i class="fa fa-sign-in icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $register; ?>"><?php echo $text_register; ?><i class="fa fa-paste icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?><i class="fa fa-key icon_list"></i></a></li>
        <?php } ?>
        <li><a class="a_icon" href="<?php echo $account; ?>"><?php echo $text_account; ?><i class="fa fa-user-circle icon_list"></i></a></li>
        <?php if ($activated && $enabled) { ?>
        <li><a class="a_icon" href="<?php echo $product; ?>"><?php echo $text_products; ?><i class="fa fa-th-list icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $question; ?>"><?php echo $text_question; ?><i class="fa fa-comment icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $review; ?>"><?php echo $text_review; ?><i class="fa fa-star icon_list"></i></a></li>
        <?php } ?>
		<li><a class="a_icon" href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?><i class="fa fa-heart icon_list"></i></a></li>
		<?php if ($activated && $enabled) { ?>
		<li><a class="a_icon" href="<?php echo $sales; ?>"><?php echo $text_sales; ?><i class="fa fa-money icon_list"></i></a></li>
		<?php } ?>
        <li><a class="a_icon" href="<?php echo $order; ?>"><?php echo $text_order; ?><i class="fa fa-list-alt icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $address; ?>"><?php echo $text_address; ?><i class="fa fa-address-book icon_list"></i></a></li>
        <?php if ($logged) { ?>
        <?php if ($activated && $enabled) { ?>
        <li><a class="a_icon" href="<?php echo $member; ?>"><?php echo $text_member; ?><i class="fa fa-user icon_list"></i></a></li>
        <?php } else {?>
        <li><a class="a_icon highlight bolder" href="<?php echo $member; ?>"><?php echo $text_activate; ?><i class="fa fa-user icon_list"></i></a></li>
        <?php } ?>
        <li><a class="a_icon" href="<?php echo $edit; ?>"><?php echo $text_edit; ?><i class="fa fa-address-card icon_list"></i></a></li>
        <?php } ?>
        <li><a class="a_icon" href="<?php echo $password; ?>"><?php echo $text_password; ?><i class="fa fa-key icon_list"></i></a></li>
        <?php if ($logged) { ?>
        <li><a class="a_icon" href="<?php echo $logout; ?>"><?php echo $text_logout; ?><i class="fa fa-sign-out icon_list icon_list_red"></i></a></li>
        <?php } ?>
    </ul>
</div>
