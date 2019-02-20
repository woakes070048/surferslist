<?php echo $header; ?>
<div class="container-page location-page">
    <div class="breadcrumb">
        <div class="layout">
            <h1><a href="<?php echo $action; ?>"><i class="fa fa-globe"></i><?php echo $heading_title; ?></a></h1>
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
            <div class="content-page">
            	<?php echo $content_top; ?>
				<?php echo $notification; ?>

                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="location-form" class="location-form">

                <div id="locationwidget" class="widget">
					<h6><?php echo $heading_sub_title; ?></h6>

					<div class="content">
						<p class="text-center text-larger"><?php echo $help_location; ?></p>

						<div class="clearafter">
							<div class="grid-2 offset-1">
                                <div class="formbox">
                                    <p class="form-label"><strong><?php echo $entry_location_country; ?></strong></p>
                                    <select name="country" id="countries">
                                        <?php foreach ($countries_select as $country_select) { ?>
                                        <option value="<?php echo $country_select['country_id']; ?>" data-code="<?php echo $country_select['iso_code_2']; ?>" <?php if ($country_select['iso_code_2'] == $country_code) { ?>selected="selected"<?php } ?>>
                                            <?php echo $country_select['name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="formbox"<?php if (!$country_code) { ?> style="display:none;"<?php } ?>>
                                    <p class="form-label"><strong><?php echo $entry_location_zone; ?></strong></p>
                                    <select name="zone" id="zones"<?php if (!$country_code) { ?>disabled="disabled"<?php } ?>>
          								<?php foreach ($zones_select as $zone_select) { ?>
          								<option value="<?php echo $zone_select['zone_id']; ?>" data-code="<?php echo $zone_select['iso_code']; ?>" <?php if ($zone_select['iso_code'] == $zone_code) { ?>selected="selected"<?php } ?>>
                                            <?php echo $zone_select['name']; ?>
                                        </option>
          								<?php } ?>
      							  </select>
                                </div>

                                <div class="formbox"<?php if (!$country_code || !$zone_code) { ?> style="display:none;"<?php } ?>>
                                    <p class="form-label"><strong><?php echo $entry_location_name; ?></strong></p>
                                    <input name="location" id="location_name" type="text" value="<?php echo $location_name; ?>" placeholder="<?php echo !$zone_code ? $text_location_na : ''; ?>"<?php if (!$zone_code) { ?>disabled="disabled"<?php } ?>/>
                                </div>

                                <div class="formbox">
                                    <a class="button button-submit button_save button_highlight bigger fullwidth"><i class="fa fa-save"></i><?php echo $button_continue; ?></a>
                                    <?php if ($redirect_path) { ?>
                                    <input type="hidden" name="redirect_path" value="<?php echo $redirect_path; ?>" />
                                    <?php } ?>
                                </div>

                                <div class="formbox">
                                    <a href="<?php echo $reset; ?>" class="button button_alt button_reset bigger fullwidth"><i class="fa fa-remove"></i><?php echo $button_remove; ?></a>
                                </div>
							</div>
						</div>

                        <p class="text-center text-larger"><?php echo $help_location_logged; ?></p>

						<div id="vmap"></div>
                        <input type="hidden" name="country_map" value="<?php echo $country_map; ?>" />
                        <input type="hidden" name="country_colors" value="<?php echo htmlspecialchars(json_encode($country_colors), ENT_COMPAT); ?>" />
                        <input type="hidden" name="zone_colors" value="<?php echo htmlspecialchars(json_encode($zone_colors), ENT_COMPAT); ?>" />

 						<p class="text-center text-smaller"><?php echo $help_location_add; ?></p>
                   </div>

                    <div class="buttons">
						<div class="left">
                            <input id="location-form-submit" type="submit" value="<?php echo $button_save; ?>" class="button hidden" />
                            <label for="location-form-submit" class="button button-submit button_save button_yes"><i class="fa fa-save"></i> <?php echo $button_save; ?></label>
                            <a href="<?php echo $reset; ?>" class="button button_remove"><i class="fa fa-remove"></i><?php echo $button_remove; ?></a>
                        </div>
                        <div class="right">
                            <a href="<?php echo $search; ?>" class="button button_search"><i class="fa fa-search"></i><?php echo $button_search; ?></a>
                            <a href="<?php echo $listings; ?>" class="button button_alt"><i class="fa fa-th-list"></i><?php echo $button_browse; ?></a>
                        </div>
                    </div>

                </div>

                </form>

                <?php echo $content_bottom; ?>
            </div>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>
