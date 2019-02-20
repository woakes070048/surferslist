<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $title; ?></title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;"><a href="<?php echo $store_url; ?>" title="<?php echo $store_name; ?>"><img src="<?php echo $logo; ?>" alt="<?php echo $store_name; ?>" style="margin-bottom: 20px; border: none;" /></a>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_welcome; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_next_step; ?></p>
  <?php if ($text_link) { ?>
  <p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?php echo $text_link; ?>"><?php echo $text_link; ?></a></p>
  <?php } ?>
  <?php if ($text_services) { ?>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_services; ?></p>
  <?php } ?>
  <?php if ($instructions) { ?>
  <table style="border-collapse: collapse; width: 100%; border-top: 1px solid #DDDDDD; border-left: 1px solid #DDDDDD; margin-bottom: 20px;">
    <thead>
      <tr>
        <td style="font-size: 12px; border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; background-color: #EFEFEF; font-weight: bold; text-align: left; padding: 7px; color: #222222;"><?php echo $text_instruction; ?></td>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($instructions as $instruction) { ?>
      <tr>
        <td style="font-size: 12px;	border-right: 1px solid #DDDDDD; border-bottom: 1px solid #DDDDDD; text-align: left; padding: 7px;"><?php echo $instruction; ?></td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <?php } ?>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_forgotten; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_thanks; ?></p>
  <p style="margin-top: 0px; margin-bottom: 40px;"><?php echo $text_signature; ?></p>
  <p style="margin-top: 0px; margin-bottom: 20px;"><?php echo $text_footer; ?></p>
</div>
</body>
</html>
