<?php echo $header; ?>
<div class="container-page account-sales-info-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><i class="fa fa-money"></i> <?php echo $heading_title; ?></h1>
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
            <div class="content-page my-account my-sale">
            	<?php echo $content_top; ?>

                <div class="widget">

					<h6><?php echo $text_sales_detail; ?></h6>

                    <div class="buttons">
                        <div class="left">
                            <a href="<?php echo $continue; ?>" class="button button_alt"><i class="fa fa-undo"></i><?php echo $button_back; ?></a>
                        </div>
                    </div>

					<div class="content">
						<div class="clearafter">
							<div class="grid-2">
								<ul class="global-attribute">
									<?php if ($invoice_no) { ?><li><b><?php echo $text_invoice_no; ?></b><i><?php echo $invoice_no; ?></i></li><?php } ?>
									<li><b><?php echo $text_sale_id; ?></b><i>#<?php echo $sale_id; ?></i></li>
									<li><b><?php echo $text_date_added; ?></b><i><?php echo $date_added; ?></i></li>
								</ul>
							</div>
							<div class="grid-2">
								<ul class="global-attribute">
									<?php if ($payment_method) { ?><li><b><?php echo $text_payment_method; ?></b><i><?php echo $payment_method; ?></i></li><?php } ?>
									<?php if ($shipping_method) { ?><li><b><?php echo $text_shipping_method; ?></b><i><?php echo $shipping_method; ?></i></li><?php } ?>
								</ul>
							</div>
						</div>

						<table class="list">
							<thead>
								<tr>
									<td class="left"><b><?php echo $text_payment_address; ?></b></td>
									<?php if ($shipping_address) { ?>
                                    <td class="left"><b><?php echo $text_shipping_address; ?></b></td>
                                    <?php } ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="left"><?php echo $payment_address; ?></td>
									<?php if ($shipping_address) { ?>
                                    <td class="left"><?php echo $shipping_address; ?></td>
                                    <?php } ?>
								</tr>
							</tbody>
						</table>

						<table class="list bordered-bottom">
							<thead>
								<tr>
									<th class="center" width="130"><?php echo $column_image; ?></th>
									<th class="left"><?php echo $column_name; ?></th>
									<th class="left hidden-very-small"><?php echo $column_manufacturer; ?></th>
									<th class="left hidden-small"><?php echo $column_model; ?></th>
									<th class="right hidden-small"><?php echo $column_price; ?></th>
									<th class="right"><?php echo $column_total; ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($products as $product) { ?>
								<tr>
									<td class="center"><div class="image image-border"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" /></div></td>
									<td class="left"><?php echo $product['name']; ?>
									<?php foreach ($product['option'] as $option) { ?>
									<br />
									<?php if ($option['type'] != 'file') { ?>
									&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
									<?php } else { ?>
									&nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
									<?php } ?>
									<?php } ?>
									<?php if ($product['quantity'] > 1) { ?>
									<br /><strong><?php echo $column_quantity; ?></strong> <?php echo $product['quantity']; ?>
									<?php } ?>
									</td>
									<td class="left hidden-very-small"><a href="<?php echo $product['manufacturer_href']; ?>"><?php echo $product['manufacturer']; ?></a></td>
									<td class="left hidden-small"><?php echo $product['model']; ?></td>
									<td class="right hidden-small"><?php echo $product['price']; ?></td>
									<td class="right"><?php echo $product['total']; ?></td>
								</tr>
								<?php } ?>
								<?php foreach ($vouchers as $voucher) { ?>
								<tr>
									<td class="center"></td>
									<td class="left"><?php echo $voucher['description']; ?></td>
									<td class="left hidden-very-small"></td>
									<td class="left hidden-small"></td>
									<td class="right hidden-small"><?php echo $voucher['amount']; ?></td>
									<td class="right"><?php echo $voucher['amount']; ?></td>
								</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<?php foreach ($totals as $total) { ?>
								<tr>
									<td  colspan="5" class="right"><b><?php echo $total['title']; ?>:</b></td>
									<td class="right"><?php echo $total['text']; ?></td>
								</tr>
								<?php } ?>
							</tfoot>
						</table>

						<?php if ($comment) { ?>
						<h3><?php echo $text_comment; ?></h3>
						<p><?php echo $comment; ?></p>
						<?php } ?>

					</div>

                </div>

				<div class="widget">
					<h6><?php echo $text_history; ?></h6>

					<div class="content">
						<div id="history"></div>

						<?php if ($this->config->get('member_report_sales_history')) { ?>
						<h3><?php echo $text_history_add; ?></h3>

						<table class="form">
						  <tr>
							<td><?php echo $text_order_status; ?></td>
							<td>
							  <select name="order_status_id">
								<?php foreach ($sales_statuses as $sales_statuses) { ?>
								<?php if ($sales_statuses['order_status_id'] == $sales_status_id) { ?>
								<option value="<?php echo $sales_statuses['order_status_id']; ?>" selected="selected"><?php echo $sales_statuses['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $sales_statuses['order_status_id']; ?>"><?php echo $sales_statuses['name']; ?></option>
								<?php } ?>
								<?php } ?>
							  </select>
							</td>
						  </tr>
						  <tr>
							<td><?php echo $text_emailed; ?></td>
							<td><input type="checkbox" name="emailed" value="0" checked="checked" /></td>
						  </tr>
						  <tr>
							<td><?php echo $text_comment; ?><br /><span class="help text-smaller"><?php echo $help_comment; ?></span></td>
							<td>
								<textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
							</td>
						  </tr>
						</table>
						<?php } ?>
					</div>

					<div class="buttons">
						<div class="left">
							<a id="button-history" class="button"><i class="fa fa-pencil"></i><?php echo $button_add_history; ?></a>
						</div>
                        <div class="right">
                            <a href="<?php echo $contact; ?>" class="button button_alt"><i class="fa fa-envelope"></i><?php echo $button_contact_member; ?></a>
                        </div>
					</div>

                </div>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<script type="text/javascript"><!--
var sale_id = <?php echo $sale_id; ?>;
var textWait = '<?php echo $text_wait; ?>';
//--></script>
<?php echo $footer; ?>
