<?php echo $header; ?>
<div class="container-page product-download-list-page">
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
            <div class="content-page my-account my-downloads">
            	<?php echo $content_top; ?>
                <div class="global-page">
					<div class="buttons">
						<div class="right">
							<a href="<?php echo $insert; ?>" class="button button_new" title="<?php echo $button_insert; ?>" rel="tooltip" data-container="body"><i class="fa fa-save"></i><?php echo $button_insert; ?></a>
							<a onclick="submit_form('<?php echo $delete; ?>');" class="button button_trash" title="<?php echo $button_delete; ?>" rel="tooltip" data-container="body"><i class="fa fa-trash"></i><span class="hidden-large"><?php echo $button_delete; ?></span></a>
						</div>
					</div>

					<div class="content">
					  <form action="" method="post" enctype="multipart/form-data" id="form" class="product-download-list-form">
						<table class="list">
						  <thead>
							<tr>
							  <td width="1" style="text-align: center;"><input type="checkbox" id="check-select-all" title="<?php echo $text_select_all; ?>" rel="tooltip" data-container="body" /></td>
							  <td class="left"><?php if ($sort == 'dd.name') { ?>
								<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
								<?php } ?></td>
							  <td class="right"><?php if ($sort == 'd.remaining') { ?>
								<a href="<?php echo $sort_remaining; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_remaining; ?></a>
								<?php } else { ?>
								<a href="<?php echo $sort_remaining; ?>"><?php echo $column_remaining; ?></a>
								<?php } ?></td>
							  <td class="right"><?php echo $column_action; ?></td>
							</tr>
						  </thead>
						  <tbody>
							<?php if ($downloads) { ?>
							<?php foreach ($downloads as $download) { ?>
							<tr>
							  <td style="text-align: center;"><?php if ($download['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?php echo $download['download_id']; ?>" checked="checked" />
								<?php } else { ?>
								<input type="checkbox" name="selected[]" value="<?php echo $download['download_id']; ?>" />
								<?php } ?></td>
							  <td class="left"><?php echo $download['name']; ?></td>
							  <td class="right"><?php echo $download['remaining']; ?></td>
							  <td class="right"><?php foreach ($download['action'] as $action) { ?>
								<a href="<?php echo $action['href']; ?>" class="button smaller"><?php echo $action['text']; ?></a>
								<?php } ?></td>
							</tr>
							<?php } ?>
							<?php } else { ?>
							<tr>
							  <td class="center" colspan="6"><?php echo $text_no_results; ?></td>
							</tr>
							<?php } ?>
						  </tbody>
						</table>
					  </form>
					  <div class="pagination"><?php echo $pagination; ?></div>
					</div>

				  <div class="buttons">
					<div class="right"><a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
				  </div>
                </div>
                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript">
var textConfirmDelete = '<?php echo $text_confirm; ?>';
</script>
<?php echo $footer; ?>
