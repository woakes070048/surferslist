<!-- JS -->
<?php if ($js_min) { ?>
<script type="text/javascript" src="<?php echo $server . 'catalog/view/' . $js_min; ?>"></script>
<?php } ?>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $server_cdn . $script; ?>"></script>
<?php } ?>
<?php if ($social_buttons) { ?>
<script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=59bda5f0d022ae0011cd87f0&product=inline-share-buttons"></script>
<?php } ?>
<?php if ($contact_enabled) { ?>
<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?onload=onloadReCaptcha&render=explicit" async defer></script>
<?php } ?>
</body>
</html>
