if (typeof textNone === 'undefined') {
    var textNone = ' --- None --- ';
}

if (typeof textWait === 'undefined') {
    var textWait = 'Please Wait';
}

if (typeof imageWidth === 'undefined') {
    var imageWidth = 205;
}

if (typeof imageHeight === 'undefined') {
    var imageHeight = 295;
}

if (typeof productNoImage === 'undefined') {
    var productNoImage = '';
}

if (typeof textYes === 'undefined') {
    var textYes = 'Yes';
}

if (typeof textNo === 'undefined') {
    var textNo = 'No';
}

if (typeof buttonClear === 'undefined') {
    var buttonClear = 'Clear';
}

if (typeof buttonRemove === 'undefined') {
    var buttonRemove = 'Remove';
}

if (typeof buttonUpload === 'undefined') {
    var buttonUpload = 'Upload';
}

if (typeof entryRequired === 'undefined') {
    var entryRequired = 'Required';
}

if (typeof csrfToken === 'undefined') {
    var csrfToken = '';
}

// Tabs
$.fn.tabsPost = function() {
	var selector = this;

	this.each(function() {
		var obj = $(this);

		$(obj.attr('href')).hide();

		$(obj).on('click', function() {
			$(selector).removeClass('selected');

			$(selector).each(function(i, element) {
				$($(element).attr('href')).hide();
			});

			$(this).addClass('selected');

			$($(this).attr('href')).show();

			$('html, body').animate({scrollTop: '165px'}, 300);

			return false;
		});
	});

	$(this).show();

	$(this).first().trigger('click');
};


var $postListingForm = $postListingForm || $('#form.account-product-form');
var $postSection = $('.post-section-container');

// Accordion
if ($postSection.length) {
    $postSection.each(function() {
        $(this).clickOrTouch('.post-section-heading', changePostSection);
    });

	$postSection.first().find('.post-section-heading').trigger('click');
}

function changePostSection(e) {
	var $headingTrigger = $(this);
	var sectionId = $headingTrigger.parent().attr('id');

	$('.post-section-active').next('.post-section-content').slideUp(400);
	$('.post-section-active').removeClass('post-section-active');

	$headingTrigger.next('.post-section-content').slideToggle(400, function() {
		$headingTrigger.toggleClass('post-section-active');

		scrollToSection('#' + sectionId, '10');
	});
}

// Trigger Click
$('.my-listing').clickOrTouch('.trigger-click', function(e) {
    var targetId = $(this).attr('data-target');
    $('#' + targetId).children(':first').trigger('click');
});

// Tabs
$('#languages a').tabsPost();

// START Text Editor
// if (typeof languages !== 'undefined' && languages.length) {
//     languages.forEach(function(language) {
//         // to-do: add new mobile-friendly rich text editor ('description' + language.id)
//     });
// }
// END Text Editor

// brand thumbnail
$postListingForm.on('change', 'select[name=\'manufacturer_id\']', function() {
	var selectedManufacturerId = this.value;

	$('#manufacturer_thumb').hide();

	if (selectedManufacturerId > 1) {
		var selectedCategoryId = $('select[name=\'category_id\']').val() || $('input[name=\'category_id\']').val();
		var selectedCategory = categories_json.length ? categories_json.filter(function(obj) {
			return obj.category_id == selectedCategoryId;
		}) : [];
		var manufacturers = selectedCategory.length ? selectedCategory[0].manufacturers : [];
		var selectedManufacturer = manufacturers.length ? manufacturers.filter(function(obj) {
			return obj.manufacturer_id == selectedManufacturerId;
		}) : [];

		if (selectedManufacturer.length) {
			if (typeof selectedManufacturer[0].image_resized !== 'undefined') {
				$('#manufacturer_thumb').attr('src', selectedManufacturer[0].image_resized);
			}

			$('#manufacturer_thumb').attr('alt', selectedManufacturer[0].name);
			$('#manufacturer_thumb').show();
		}
	}
});

// START Image
var urlImageUpload = 'upload-image?width=' + imageWidth + '&height=' + imageHeight + '&type=listing&csrf_token=' + csrfToken;  // encodeURIComponent

function uploadImage(image_id, url, no_image) {
	new AjaxUpload('#button-upload' + image_id, {
		action: url,
		name: 'file',
		autoSubmit: true,
		responseType: 'json',
		onSubmit: beforeImageUpload(image_id, no_image),
		onComplete: afterImageUpload(image_id, no_image)
	});
}

function clipboardPasteImage(image_id, url, no_image, handle_error) {
	var _self = this;
    var formData = new FormData();

	document.addEventListener('paste', function (e) {
        _self.onPasteClipboard(e);
    }, false);

	this.onPasteClipboard = function (e) {
		if (e.clipboardData) {
			var items = e.clipboardData.items;
            var isImage = false;

			if (!items) {
                return;
            }

			for (var i = 0; i < items.length; i++) {
				if (items[i].type.indexOf("image") !== -1) {
					var blob = items[i].getAsFile();
					var URLObj = window.URL || window.webkitURL;
					var source = URLObj.createObjectURL(blob);

                    formData.set('file', blob, blob.name);
                    isImage = true;
				}
			}

            if (isImage && formData.has('file') && formData.getAll('file').length) {
                _self.sendImage();
                e.preventDefault();
            }
		}
	}

    this.sendImage = function (e) {
        // postDataImageAjax(url, formData, image_id, no_image, handle_error);
        postDataImageXhr(url, formData, image_id, no_image);
    }
}

function postDataImageAjax(url, data, image_id, no_image, handle_error) {
    $.ajax({
        url: url,
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        type: 'POST',
        beforeSend: beforeImageUpload(image_id, no_image),
        complete: function() { },
        success: afterImageUpload(image_id, no_image, false),
        error: handle_error
    });
}

function postDataImageXhr(url, data, image_id, no_image) {
    var xhr = new XMLHttpRequest();
    var callback = afterImageUpload(image_id, no_image, false);

    xhr.open('POST', url, true);

    xhr.upload.onloadstart = beforeImageUpload(image_id, no_image);

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            var percentComplete = (e.loaded / e.total) * 100;
            $('.progress-bar').css('width', percentComplete + '%');
        }
    }

    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {
            var json = JSON.parse(xhr.responseText);
            callback(json);
        }
    }

    xhr.send(data);
}

function beforeImageUpload(image_id, no_image) {
    return function(file, extension) {
        $('.image' + image_id + '-notification').fadeOut(300).remove();
        $('#thumb' + image_id).css('opacity', '0.5');
        //$('#thumb' + image_id).attr('src', no_image);
        $('#thumb' + image_id).after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
        $('#button-upload' + image_id).prop('disabled', true);
    }
}

function afterImageUpload(image_id, no_image, is_ajax_upload = true) {
    var callback = function(file, json) {
        $('#button-upload' + image_id).prop('disabled', false);
        $('#thumb' + image_id).animate({'opacity': '1'}, 300, 'swing');

        if (json['success']) {
            $('#image' + image_id).val(json['filename']);
            $('#thumb' + image_id).after('<p class="image' + image_id + '-notification" id="image' + image_id + '-success"><span class="successful"><i class="fa fa-check-sign"></i> ' + json['success'] + '</span></p>');
            $('#thumb' + image_id).replaceWith('<img src="' + json['thumb'] + '" alt="" id="thumb' + image_id + '" class="thumb" />');
        }

        if (json['error']) {
            $('#image' + image_id).val('');
            $('#thumb' + image_id).after('<p class="image' + image_id + '-notification" id="image' + image_id + '-error"><span class="error"><i class="fa fa-exclamation-triangle"></i> ' + json['error'] + '</span></p>');
            $('#thumb' + image_id).replaceWith('<img src="' + no_image + '" alt="" id="thumb' + image_id + '" class="thumb" />');
        }

        setTimeout(function() {
            $('#thumb' + image_id).siblings('.wait').fadeOut(300).remove();
        }, 300);
    }

    if (is_ajax_upload) {
        return callback
    } else {
        return function(json) {
            callback('', json);
        }
    }
}

// primary image upload by button
uploadImage('', urlImageUpload, productNoImage);

// primary image upload by copy & paste
var formDataImage = new clipboardPasteImage('', urlImageUpload, productNoImage, handleError);


// additional images
$.each($('#tab-image .image-row'), function(index, element) {
	uploadImage(index, urlImageUpload, productNoImage);
});

function removeImage(this_image_row) {
	$('#image-row' + this_image_row).remove();
	image_row--;
}

function imageMaxWarning() {
    alert(errorMaxImages);
}

$('#add-image').clickOrTouch(null, function() {
	if (image_row >= imageMax){
		imageMaxWarning();
		return;
	}

    html  = '<div id="image-row' + image_row + '" class="image-row">';
	html += '    <div class="image image-border">';
	html += '        <img src="' + productNoImage + '" alt="" id="thumb' + image_row + '" class="thumb" /><br />';
	html += '        <input type="hidden" name="product_image[' + image_row + '][image]" value="" id="image' + image_row + '" /><input type="hidden" name="product_image[' + image_row + '][sort_order]" value="' + image_row + '" size="2" />';

    if (typeof permissionsInventoryEnabled !== 'undefined' && permissionsInventoryEnabled) {
        html += '        <a data-image-row="' + image_row + '" class="button button_images button_alt upload-images"><i class="fa fa-file"></i> ' + textBrowse + '</a>';
    }

    html += '        <a class="button button-upload" id="button-upload' + image_row + '"><i class="fa fa-upload"></i> ' + buttonUpload + '</a>';
    html += '        <a class="button button_clear button_alt" title="' + buttonClear + '" data-target="' + image_row + '" rel="tooltip" data-container="body"><i class="fa fa-times"></i> ' + buttonClear + '</a>';
    html += '        <a onclick="removeImage(' + image_row + ')" class="button button_trash button_remove" title="' + buttonRemove + '" rel="tooltip" data-container="body"><i class="fa fa-trash"></i> ' + buttonRemove + '</a>';
    html += '   </div>';
	html += '   </div>';
	html += '</div>';

	$('#tab-image .image-row-footer').before(html);

	uploadImage(image_row, urlImageUpload, productNoImage);

	image_row++;
});

$postListingForm.clickOrTouch('.button_clear', function(e) {
    e.preventDefault();
    var targetId = $(this).attr('data-target');  // empty string or $image_row
	$('.image' + targetId + '-notification').fadeOut(300).remove();
    $('#thumb' + targetId).attr('src', productNoImage);
    $('#image' + targetId).val('');
});

// image manager
$(document).clickOrTouch('.upload-images', function(e) {
	e.preventDefault();
	var imageRowId = $(this).attr('data-image-row');
	uploadImages('image' + imageRowId, 'thumb' + imageRowId);
});

function uploadImages(imageId, thumbId) {
	$('#dialog').remove();

	$('.container-left').prepend('<div id="dialog"><iframe src="filemanager?field=' + encodeURIComponent(imageId) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

    $('#dialog').dialog({
		title: textImageManager,
		open: function (e, ui) {
			setTimeout(function() {
				$('.ui-widget-overlay').clickOrTouch(null, function () {
					$(this).siblings('.ui-dialog').find('.ui-dialog-content').dialog('close');
				});
			}, 2000);
		},
		close: function (event, ui) {
			$('.' + imageId + '-notification').fadeOut(300).remove();

			if ($('#' + imageId).val()) {
				$.ajax({
					url: 'filemanager-image',
                    type: 'get',
                    data: 'field=' + encodeURIComponent(imageId) + '&image=' + encodeURIComponent($('#' + imageId).val()),
					dataType: 'json',
					success: function(json) {
						$('#' + thumbId).replaceWith('<img src="' + json + '" alt="" id="' + thumbId + '" class="thumb" />');
					}
				});
			}
		},
		width: '85%',
		height: 540,
		resizable: false,
		modal: true,
		position: { my: "center top", at: "center top+10", of: ".container-page" }
	});
};
// END Image

// START Sale
$('#shipping-custom').clickOrTouch('#shipping-apply-all', function(e) {
    e.preventDefault();
    $('#shipping-custom').find(':text').val($('input[id=\'apply_all\']').val());
});

$(document).ready(function () {
	if ($('input[name=\'for_sale\']:checked').val() == 1) {
		$('#for-sale-section').show();
    	$('#listing-value-section').hide();
	} else {
		$('#for-sale-section').hide();
    	$('#listing-value-section').show();
	}

	$('input[name=\'for_sale\']').on('change', function () {
		if ($(this).val() == 1) {
			$('#for-sale-section').show();
        	$('#listing-value-section').hide();
		} else {
			$('#for-sale-section').hide();
        	$('#listing-value-section').show();
		}
	});

	if ($('select[name=\'type\']').val() == 0) {
		$('#buy-now-options').hide();
	} else {
		if (!$('input[name=\'quantity\']').val() || $('input[name=\'quantity\']').val() == 0) {
			$('input[name=\'quantity\']').val('1');
		}
		$('#buy-now-options').show();
	}

	$('select[name=\'type\']').on('change', function () {
		if ($(this).val() == 0) {
			$('#buy-now-options').hide();
		} else {
			if (!$('input[name=\'quantity\']').val() || $('input[name=\'quantity\']').val() == 0) {
				$('input[name=\'quantity\']').val('1');
			}
			$('#buy-now-options').show();
		}
	});

	if ($('input[name=\'shipping\']:checked').val() == 1) {
		$('#shipping-section').show();
	} else {
		$('#shipping-section').hide();
	}

	$('input[name=\'shipping\']').on('change', function () {
		if ($(this).val() == 1) {
			$('#shipping-section').show();
		} else {
			$('#shipping-section').hide();
		}
	});
});
// END Sale

// START Download
if ($('#tab-download').length) {
    $('#product-download .product-download-row').each(function(index) {
        //var productDownloadRow = $(this).attr('data-row');
        $(this).clickOrTouch('.button_remove', function(e) {
            e.preventDefault();
            $(this).closest('.product-download-row').remove();
            //$('#product-download-row' + productDownloadRow).remove();
        });
    });

    function upload_file(download_id) {
    	new AjaxUpload('#file-upload' + download_id, {
    		action: 'upload-file-member',
    		name: 'file',
    		autoSubmit: false,
    		responseType: 'json',
    		onChange: function(file, extension) {
    			this.submit();
    		},
    		onSubmit: function(file, extension) {
    			$('#file-upload' + download_id).after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
    			$('#file-upload' + download_id).prop('disabled', true);
    		},
    		onComplete: function(file, json) {
    			$('#file-upload' + download_id).prop('disabled', false);
    			if (json['success']) {
    				alert(json['success']);

    				$('#filename' + download_id).val(json['filename']);
    				$('#mask' + download_id).val(json['mask']);
    			}

    			if (json['error']) {
    				alert(json['error']);
    			}

    			$('#loading').remove();
    		}
    	});
    }

    $.each($('#product-download tbody'), function(index, element) {
    	upload_file(index);
    });

    function addDownload() {
    	html  = '<tbody id="product-download-row' + product_download_row + '">';
    	html += '  <tr>';
    	html += '    <td class="left"><input type="text" name="product_download[' + product_download_row + '][name]" value="" size="40" /><input type="hidden" name="product_download[' + product_download_row + '][product_download_id]" value="" /></td>';
    	html += '    <td class="left"><input type="text" readonly="readonly" size="40" name="product_download[' + product_download_row + '][mask]" value="" id="mask' + product_download_row + '" /> <a id="file-upload' + product_download_row + '" class="button">' + buttonUploadDigitalDownload + '</a><input type="hidden" name="product_download[' + product_download_row + '][filename]" value="" id="filename' + product_download_row + '" /><input type="hidden" name="product_download[' + product_download_row + '][remaining]" value="1" id="remaining' + product_download_row + '" /></td>';
    	html += '    <td class="right"><a class="button button_remove" onclick="$(\'#product-download-row' + product_download_row + '\').remove();" >' + buttonRemove + '</a></td>';
    	html += '  </tr>';
    	html += '</tbody>';

    	$('#product-download tfoot').before(html);

    	upload_file(product_download_row);

    	product_download_row++;
    }
}
// END Download

// START Attribute
if ($('#tab-attribute').length) {
    $('#attribute .attribute-row').each(function(index) {
        $(this).clickOrTouch('.button_remove', function(e) {
            e.preventDefault();
            $(this).closest('.attribute-row').remove();
        });

        $(this).child('input[name^=\'product_attribute\']').clickOrTouch(null, function(){
            $(this).catcomplete("search");
        });
    });

    function addAttribute() {
    	html  = '<tbody id="attribute-row' + attribute_row + '">';
        html += '  <tr>';
    	html += '    <td class="left"><input type="text" name="product_attribute[' + attribute_row + '][name]" value="" /><input type="hidden" name="product_attribute[' + attribute_row + '][attribute_id]" value="" /></td>';
    	html += '    <td class="left">';

        languages.forEach(function(language) {
            html += '<textarea name="product_attribute[' + attribute_row + '][product_attribute_description][' + language.id + '][text]" cols="40" rows="5"></textarea><img src="image/flags/' + language.image + '" title="' + language.name + '" align="top" /><br />';
        });

    	html += '    </td>';
    	html += '    <td class="left"><a onclick="$(\'#attribute-row' + attribute_row + '\').remove();" class="button button_remove">' + buttonRemove + '</a></td>';
        html += '  </tr>';
        html += '</tbody>';

    	$('#attribute tfoot').before(html);

    	attributeautocomplete(attribute_row);

    	attribute_row++;
    }

    $.widget('custom.catcomplete', $.ui.autocomplete, {
    	_renderMenu: function(ul, items) {
    		var self = this, currentCategory = '';

    		$.each(items, function(index, item) {
    			if (item.category != currentCategory) {
    				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');

    				currentCategory = item.category;
    			}

    			self._renderItem(ul, item);
    		});
    	}
    });

    function attributeautocomplete(attribute_row) {
    	$('input[name=\'product_attribute[' + attribute_row + '][name]\']').catcomplete({
    		delay: 0,
    		source: function(request, response) {
    			$.ajax({
    				url: 'autocomplete-attribute?filter_name=' +  encodeURIComponent(request.term),
    				dataType: 'json',
    				success: function(json) {
    					response($.map(json, function(item) {
    						return {
    							category: item.attribute_group,
    							label: item.name,
    							value: item.attribute_id
    						}
    					}));
    				}
    			});
    		},
    		select: function(event, ui) {
    			$('input[name=\'product_attribute[' + attribute_row + '][name]\']').val(ui.item.label);
    			$('input[name=\'product_attribute[' + attribute_row + '][attribute_id]\']').val(ui.item.value);

    			return false;
    		},
    		focus: function(event, ui) {
          		return false;
       		}
    	});
    }

    $('#attribute tbody').each(function(index, element) {
    	attributeautocomplete(index);
    });
}
// END Attribute

// START Option
if ($('#tab-option').length) {

    $('#tab-option #vtab-option a').each(function(index) {
        var optionRow = $(this).attr('data-row');
        $(this).clickOrTouch('.icon_remove', function(e) {
            e.preventDefault();
            $('#option-' + optionRow).remove();
            $('#tab-option-' + optionRow).remove();
            $('#vtabs a:first').trigger('click');
            return false;
        });
    });

    $('#tab-option .option-row').each(function(i) {
        var optionValueRows = $(this).find('.option-value-row');

        optionValueRows.each(function(j) {
            $(this).clickOrTouch('.button_remove', function(e) {
                e.preventDefault();
                $(this).closest('.option-value-row').remove();
            });
        })
    });

    $('input[name=\'option\']').catcomplete({
    	minLength: 0,
    	delay: 100,
    	source: function(request, response) {
    		$.ajax({
    			url: 'autocomplete-option?filter_name=' +  encodeURIComponent(request.term),
    			dataType: 'json',
    			success: function(json) {
    				response($.map(json, function(item) {
    					return {
    						category: item.category,
    						label: item.name,
    						value: item.option_id,
    						type: item.type,
    						option_value: item.option_value
    					}
    				}));
    			}
    		});
    	},
    	select: function(event, ui) {
    		html  = '<div id="tab-option-' + option_row + '" class="vtabs-content">';
    		html += '	<input type="hidden" name="product_option[' + option_row + '][product_option_id]" value="" />';
    		html += '	<input type="hidden" name="product_option[' + option_row + '][name]" value="' + ui.item.label + '" />';
    		html += '	<input type="hidden" name="product_option[' + option_row + '][option_id]" value="' + ui.item.value + '" />';
    		html += '	<input type="hidden" name="product_option[' + option_row + '][type]" value="' + ui.item.type + '" />';
    		html += '	<table class="form">';
    		html += '	  <tr>';
    		html += '		<td>' + entryRequired + '</td>';
    		html += '       <td><select name="product_option[' + option_row + '][required]">';
    		html += '	      <option value="1">' + textYes + '</option>';
    		html += '	      <option value="0">' + textNo + '</option>';
    		html += '	    </select></td>';
    		html += '     </tr>';

    		if (ui.item.type == 'text') {
    			html += '     <tr>';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><input type="text" name="product_option[' + option_row + '][option_value]" value="" /></td>';
    			html += '     </tr>';
    		}

    		if (ui.item.type == 'textarea') {
    			html += '     <tr>';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><textarea name="product_option[' + option_row + '][option_value]" cols="40" rows="5"></textarea></td>';
    			html += '     </tr>';
    		}

    		if (ui.item.type == 'file') {
    			html += '     <tr style="display: none;">';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><input type="text" name="product_option[' + option_row + '][option_value]" value="" /></td>';
    			html += '     </tr>';
    		}

    		if (ui.item.type == 'date') {
    			html += '     <tr>';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><input type="text" name="product_option[' + option_row + '][option_value]" value="" class="date" /></td>';
    			html += '     </tr>';
    		}

    		if (ui.item.type == 'datetime') {
    			html += '     <tr>';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><input type="text" name="product_option[' + option_row + '][option_value]" value="" class="datetime" /></td>';
    			html += '     </tr>';
    		}

    		if (ui.item.type == 'time') {
    			html += '     <tr>';
    			html += '       <td>' + entryOptionValue + '</td>';
    			html += '       <td><input type="text" name="product_option[' + option_row + '][option_value]" value="" class="time" /></td>';
    			html += '     </tr>';
    		}

    		html += '  </table>';

    		if (ui.item.type == 'select' || ui.item.type == 'radio' || ui.item.type == 'checkbox' || ui.item.type == 'image') {
    			html += '  <table id="option-value' + option_row + '" class="list">';
    			html += '  	 <thead>';
    			html += '      <tr>';
    			html += '        <td class="left">' + entryOptionValue + '</td>';
    			html += '        <td class="right">' + entryQuantity + '</td>';
    			html += '        <td class="left">' + entrySubtract + '</td>';
    			html += '        <td class="right">' + entryPrice + '</td>';
    			html += '        <td class="right">' + entryOptionPoints + '</td>';
    			html += '        <td class="right">' + entryWeight + '</td>';
    			html += '        <td></td>';
    			html += '      </tr>';
    			html += '  	 </thead>';
    			html += '    <tfoot>';
    			html += '      <tr>';
    			html += '        <td colspan="6"></td>';
    			html += '        <td class="left"><a onclick="addOptionValue(' + option_row + ');" class="button">' + buttonAddOptionValue + '</a></td>';
    			html += '      </tr>';
    			html += '    </tfoot>';
    			html += '  </table>';
                html += '  <select id="option-values' + option_row + '" style="display: none;">';

                for (i = 0; i < ui.item.option_value.length; i++) {
    				html += '  <option value="' + ui.item.option_value[i]['option_value_id'] + '">' + ui.item.option_value[i]['name'] + '</option>';
                }

                html += '  </select>';
    			html += '</div>';
    		}

    		$('#tab-option').append(html);

    		$('#option-add').before('<a href="#tab-option-' + option_row + '" id="option-' + option_row + '">' + ui.item.label + '&nbsp;<i class="fa fa-times-circle" alt="" onclick="$(\'#option-' + option_row + '\').remove(); $(\'#tab-option-' + option_row + '\').remove(); $(\'#vtab-option a:first\').trigger(\'click\'); return false;"><i></a>');

    		$('#vtab-option a').tabsPost();

    		$('#option-' + option_row).trigger('click');

    		$('.date').datepicker({dateFormat: 'yy-mm-dd'});
    		$('.datetime').datetimepicker({
    			dateFormat: 'yy-mm-dd',
    			timeFormat: 'h:m'
    		});

    		$('.time').timepicker({timeFormat: 'h:m'});

    		option_row++;

    		return false;
    	},
    	focus: function(event, ui) {
          return false;
       }
    });

    function addOptionValue(option_row) {
    	html  = '<tbody id="option-value-row' + option_value_row + '">';
    	html += '  <tr>';
    	html += '    <td class="left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][option_value_id]">';
    	html += $('#option-values' + option_row).html();
    	html += '    </select><input type="hidden" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][product_option_value_id]" value="" /></td>';
    	html += '    <td class="right"><input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][quantity]" value="" size="3" /></td>';
    	html += '    <td class="left"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][subtract]">';
    	html += '      <option value="1">' + textYes + '</option>';
    	html += '      <option value="0">' + textNo + '</option>';
    	html += '    </select></td>';
    	html += '    <td class="right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price_prefix]">';
    	html += '      <option value="+">+</option>';
    	html += '      <option value="-">-</option>';
    	html += '    </select>';
    	html += '    <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][price]" value="" size="5" /></td>';
    	html += '    <td class="right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][points_prefix]">';
    	html += '      <option value="+">+</option>';
    	html += '      <option value="-">-</option>';
    	html += '    </select>';
    	html += '    <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][points]" value="" size="5" /></td>';
    	html += '    <td class="right"><select name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight_prefix]">';
    	html += '      <option value="+">+</option>';
    	html += '      <option value="-">-</option>';
    	html += '    </select>';
    	html += '    <input type="text" name="product_option[' + option_row + '][product_option_value][' + option_value_row + '][weight]" value="" size="5" /></td>';
    	html += '    <td class="left"><a onclick="$(\'#option-value-row' + option_value_row + '\').remove();" class="button button_remove">' + buttonRemove + '</a></td>';
    	html += '  </tr>';
    	html += '</tbody>';

    	$('#option-value' + option_row + ' tfoot').before(html);

    	option_value_row++;
    }

    $('.date').datepicker({dateFormat: 'yy-mm-dd'});
    $('.datetime').datetimepicker({
    	dateFormat: 'yy-mm-dd',
    	timeFormat: 'h:m'
    });
    $('.time').timepicker({timeFormat: 'h:m'});

    $('#vtab-option a').tabsPost();
    $('input[name=\'option\']').clickOrTouch(null, function(){
        $(this).catcomplete("search");
    });
    $('#option-add-button').clickOrTouch(null, function(){
        $('input[name=\'option\']').catcomplete("search");
    });
}
// END Option

// Related
$('input[name=\'related\']').clickOrTouch(null, function(){
    $(this).autocomplete("search");
});

// Status
$('.enable-disable-buttons').clickOrTouch('a', function () {
    var data_value = $(this).attr('data-value');
    var selected_class = data_value == 'true' || data_value == '1' ? 'button_yes' : 'button_no';
    $(this).removeClass('button_cancel');
    $(this).addClass(selected_class);
    $(this).parent().siblings('div').children('.button').addClass('button_cancel');
    $(this).parent().nextAll('input').val(data_value);
});
