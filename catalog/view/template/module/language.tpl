<?php if (count($languages) > 1) { ?>
<div id="hboxlanguage" class="hbox has-menu">
    <div class="heading">
        <span class="span-title"><i class="fa fa-language" title="<?php echo $text_language; ?>"></i><?php echo $text_language; ?></span>
    </div>
    <div class="content">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
            <ul id="language" class="list">
                <?php foreach ($languages as $language) { ?>
                <li>
                    <a data-code="<?php echo $language['code']; ?>"><img src="image/flags/<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a>
                </li>
                <?php } ?>
            </ul>
            <input type="hidden" name="language_code" value="" />
            <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        </form>
    </div>
</div>
<?php } ?>
