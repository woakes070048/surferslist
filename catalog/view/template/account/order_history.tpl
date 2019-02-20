<table class="list bordered-bottom">
  <thead>
    <tr>
      <th class="left date"><b><?php echo $column_date_added; ?></b></th>
      <th class="left"><b><?php echo $column_status; ?></b></th>
      <th class="left hidden-small"><b><?php echo $column_comment; ?></b></th>
      <th class="left hidden-very-small"><b><?php echo $column_author; ?></b></th>
      <th class="left"><b><?php echo $column_emailed; ?></b></th>
    </tr>
  </thead>
  <tbody>
    <?php if ($histories) { ?>
    <?php foreach ($histories as $history) { ?>
    <tr>
      <td class="left date"><?php echo $history['date_added']; ?></td>
      <td class="left hidden-small"><?php echo $history['status']; ?></td>
      <td class="left show-small"><a title="<?php echo $history['member']; ?>" data-content="<?php echo $history['comment_full']; ?>" data-placement="top" rel="popover"><?php echo $history['status']; ?></a></td>
      <td class="left hidden-small text-smaller"><?php echo $history['comment_full']; ?></td>
      <td class="left hidden-very-small"><?php echo $history['member']; ?></td>
      <td class="left"><?php echo $history['emailed']; ?></td>
    </tr>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td class="center" colspan="4"><?php echo $text_no_history; ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
<?php if ($error) { ?>
<div class="warning">
	<p><?php echo $error; ?></p>
	<span class="close"><i class="fa fa-times"></i></span>
	<span class="icon"><i class="fa fa-exclamation-triangle"></i></span>
</div>
<?php } ?>
<?php if ($success) { ?>
<div class="success">
	<p><?php echo $success; ?></p>
	<span class="close"><i class="fa fa-times"></i></span>
	<span class="icon"><i class="fa fa-check"></i></span>
</div>
<?php } ?>
