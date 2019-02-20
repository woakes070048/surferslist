<?php echo $header; ?>
<div class="container-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><?php echo $heading_title; ?></h1>
            <div class="links">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="layout">
        <?php echo $column_left; ?>
        <div class="container-center">
            <div class="content-page my-account my-rewards">
            	<?php echo $content_top; ?>
                <div class="global-page">
                	<p><?php echo $text_total; ?> <strong><?php echo $total; ?></strong>.</p>
                    <table class="list">
                        <thead>
                            <tr>
                                <td class="left"><?php echo $column_date_added; ?></td>
                                <td class="left"><?php echo $column_description; ?></td>
                                <td class="right"><?php echo $column_points; ?></td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($rewards) { ?>
                            <?php foreach ($rewards  as $reward) { ?>
                            <tr>
                                <td class="left"><?php echo $reward['date_added']; ?></td>
                                <td class="left">
                                    <?php if ($reward['order_id']) { ?>
                                    <a href="<?php echo $reward['href']; ?>"><?php echo $reward['description']; ?></a>
                                    <?php } else { ?>
                                    <?php echo $reward['description']; ?>
                                    <?php } ?>
                                </td>
                                <td class="right"><?php echo $reward['points']; ?></td>
                            </tr>
                            <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td class="center" colspan="3"><?php echo $text_empty; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="pagination"><?php echo $pagination; ?></div>
                    <div class="buttons">
                        <div class="right"><a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_continue; ?></a></div>
                    </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
