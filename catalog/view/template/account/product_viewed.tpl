<?php echo $header; ?>
<div class="container-page product-viewed-page">
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
            <div class="content-page my-account my-views">
            	<?php echo $content_top; ?>
                <div class="global-page">

					<?php if ($error_warning) { ?>
					<div class="warning">
						<p><?php echo $error_warning; ?></p>
						<span class="close"><i class="fa fa-times"></i></span>
						<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
					</div>
					<?php } ?>

            	<h4><?php echo $text_member_views; ?></h4>

            	<form action="" method="post" enctype="multipart/form-data" id="form" class="product-viewed-form">
            	  <table class="list">
            		<thead>
            		  <tr>
            			<td class="center"><?php echo $column_image; ?></td>
            			<td class="left"><?php if ($sort == 'pd.name') { ?>
            				<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
            				<?php } else { ?>
            				<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
            				<?php } ?></td>
            			<td class="left"><?php if ($sort == 'p.model') { ?>
            				<a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
            				<?php } else { ?>
            				<a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
            				<?php } ?></td>
            			<td width="90" class="left"><?php if ($sort == 'p.status') { ?>
            				<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
            				<?php } else { ?>
            				<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
            				<?php } ?></td>
            			<td class="left"><?php if ($sort == 'p.date_added') { ?>
            				<a href="<?php echo $sort_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date; ?></a>
            				<?php } else { ?>
            				<a href="<?php echo $sort_date; ?>"><?php echo $column_date; ?></a>
            				<?php } ?></td>
            			<td class="left"><?php if ($sort == 'p.viewed') { ?>
            				<a href="<?php echo $sort_viewed; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_viewed; ?></a>
            				<?php } else { ?>
            				<a href="<?php echo $sort_viewed; ?>"><?php echo $column_viewed; ?></a>
            				<?php } ?></td>
            			<td class="right"><?php echo $column_percent; ?></td>
            		  </tr>
            		</thead>
            		<tbody>
            			<tr class="filter">
            			  <td></td>
            			  <td><input type="text" name="filter_name" value="<?php echo $filter_name; ?>" /></td>
            			  <td><input type="text" name="filter_model" value="<?php echo $filter_model; ?>" /></td>
            			  <td><select name="filter_status">
            				  <option value="*"></option>
            				  <?php if ($filter_status) { ?>
            				  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
            				  <?php } else { ?>
            				  <option value="1"><?php echo $text_enabled; ?></option>
            				  <?php } ?>
            				  <?php if (!is_null($filter_status) && !$filter_status) { ?>
            				  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
            				  <?php } else { ?>
            				  <option value="0"><?php echo $text_disabled; ?></option>
            				  <?php } ?>
            				</select></td>
            			  <td></td>
            			  <td></td>
            			  <td align="right"><a onclick="filterProductViewed();" class="button"><?php echo $button_filter; ?></a></td>
            			</tr>
            		  <?php if ($products) { ?>
            		  <?php foreach ($products as $product) { ?>
            		  <tr>
            			<td class="center"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="padding: 1px; border: 1px solid #DDDDDD;" /></td>
            			<td class="left"><a href="<?php echo $product['href']; ?>" target="_blank"><?php echo $product['name']; ?></a></td>
            			<td class="left"><?php echo $product['model']; ?></td>
            			<td class="left <?php echo (($product['status'] === $text_enabled) ? ('enabled') : ('disabled')); ?>"><?php echo $product['status']; ?></td>
            			<td class="right"><?php echo $product['date']; ?></td>
            			<td class="right"><?php echo $product['viewed']; ?></td>
            			<td class="right"><?php echo $product['percent']; ?></td>
            		  </tr>
            		  <?php } ?>
            		  <?php } else { ?>
            		  <tr>
            			<td class="center" colspan="7"><div class="alert warning"><?php echo $text_no_results; ?></div></td>
            		  </tr>
            		  <?php } ?>
            		</tbody>
            	  </table>
            	  </form>

            	  <div class="pagination"><?php echo $pagination; ?></div>

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
<?php echo $footer; ?>
