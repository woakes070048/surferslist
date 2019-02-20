<div class="widget">
	<h6><?php echo $heading_title; ?></h6>
    <ul class="widget-list">
        <?php foreach ($informations as $information) { ?>
        <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
        <?php } ?>
        <li><a class="a_icon" href="<?php echo $contact; ?>"><?php echo $text_contact; ?><i class="fa fa-envelope icon_list"></i></a></li>
        <li><a class="a_icon" href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?><i class="fa fa-sitemap icon_list"></i></a></li>
    </ul>
</div>
