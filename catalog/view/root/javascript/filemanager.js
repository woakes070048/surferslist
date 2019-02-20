if (typeof imageDirectory === 'undefined') {
    var imageDirectory = 'member';
}

if (typeof urlImages === 'undefined') {
    var urlImages = '';
}

if (typeof textEditor === 'undefined') {
    var textEditor = false;
}

if (typeof textLoadingImage === 'undefined') {
    var textLoadingImage = 'Loading image:';
}

if (typeof textSuccessImageUploaded === 'undefined') {
    var textSuccessImageUploaded = 'Success! Image uploaded.';
}

if (typeof textErrorDirectory === 'undefined') {
    var textErrorDirectory = 'Please select a directory!';
}

if (typeof textConfirmDelete === 'undefined') {
    var textConfirmDelete = 'Confirm DELETE?';
}

if (typeof textErrorSelect === 'undefined') {
    var textErrorSelect = 'Please select a file!';
}

if (typeof textWarning === 'undefined') {
    var textWarning = 'Warning!';
}

if (typeof entryFolder === 'undefined') {
    var entryFolder = 'New Folder:';
}

if (typeof entryCopy === 'undefined') {
    var entryCopy = 'New Filename:';
}

if (typeof buttonFolder === 'undefined') {
    var buttonFolder = 'New Folder';
}

if (typeof buttonCopy === 'undefined') {
    var buttonCopy = 'Copy';
}

if (typeof buttonSelect === 'undefined') {
    var buttonCopy = 'Select';
}

if (typeof buttonApply === 'undefined') {
    var buttonApply = 'Apply';
}

if (typeof buttonUpload === 'undefined') {
    var buttonUpload = 'Upload New';
}

if (typeof buttonSubmit === 'undefined') {
    var buttonSubmit = 'Submit';
}

if (typeof fieldTarget === 'undefined') {
    var fieldTarget = '';
}

$(document).ready(function() {
	(function(){
		var special = jQuery.event.special,
			uid1 = 'D' + (+new Date()),
			uid2 = 'D' + (+new Date() + 1);

		special.scrollstart = {
			setup: function() {
				var timer,
					handler =  function(evt) {
						var _self = this,
							_args = arguments;

						if (timer) {
							clearTimeout(timer);
						} else {
							evt.type = 'scrollstart';
							jQuery.event.dispatch.apply(_self, _args);
						}

						timer = setTimeout( function(){
							timer = null;
						}, special.scrollstop.latency);

					};

				jQuery(this).on('scroll', handler).data(uid1, handler);
			},
			teardown: function(){
				jQuery(this).off('scroll', jQuery(this).data(uid1) );
			}
		};

		special.scrollstop = {
			latency: 0,
			setup: function() {

				var timer,
						handler = function(evt) {

						var _self = this,
							_args = arguments;

						if (timer) {
							clearTimeout(timer);
						}

						timer = setTimeout( function(){

							timer = null;
							evt.type = 'scrollstop';
							jQuery.event.dispatch.apply(_self, _args);

						}, special.scrollstop.latency);

					};

				jQuery(this).on('scroll', handler).data(uid2, handler);

			},
			teardown: function() {
				jQuery(this).off('scroll', jQuery(this).data(uid2));
			}
		};
	})();

    // use 'touchstart' if touch device, else use mouse event
    // var clickEvent = ('ontouchstart' in window) ? 'touchstart' : 'click';
    $.fn.clickOrTouch = function(selector, handler) {
        this.on("touchend", selector, function(e) {
            handler.call(this, e);
            e.stopPropagation();
            e.preventDefault();
        });
        this.on("click", selector, function(e) {
            handler.call(this, e);
        });
        return this;
    };

	function loadImage(image_anchor) {
		if ($(image_anchor).length) {
			var imgFile = $(image_anchor).next('.image-details').find('input[name=\'image\']').val(); // e.g. "/file-name-001.jpg"

			$.ajax({
				url: 'filemanager-image',
                type: 'get',
                data: 'image=' + encodeURIComponent(imgFile),
				dataType: 'json',
				beforeSend: function() {
					$(image_anchor).parent('div').css({'opacity': '0', 'visibility': 'visible'}).animate({'opacity': '1'}, 600, 'swing');
				},
				success: function(json) {
                    if (json) {
                        newImage = new Image();

    					newImage.onload = function() {
    						$(image_anchor).replaceWith('<img src="' + json + '" alt="" title="" class="filemanager-thumb" />');
    					}

    					newImage.src = json;
                    }
				}
			});
		}
	}

	$('#my-images').on('scrollstop', function() {
        var loadFrame = $('#my-images').height() + 300;

		$('#my-images .loading-image').each(function(index, element) {
			var offset = $(element).offset();

			if ((offset.top > 0) && (offset.top < loadFrame)) {
				loadImage(element);
			}
		});
	});

    function loadImages(dir) {
        $.ajax({
            url: 'filemanager-files',
            type: 'post',
            data: 'directory=' + dir,
            dataType: 'json',
            success: function(json) {
                html = '';

                if (json) {
                    for (i = 0; i < json.length; i++) {
                        html += '<div style="visibility:hidden;">';

                        if (json[i]['img'].length) {
                            html += '<img src="' + json[i]['img'] + '" alt="" title="" class="filemanager-thumb" />';
                        } else {
                            html += '<span class="loading-image" title="' + textLoadingImage + ' ' + json[i]['filename'] + '"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>';
                        }

                        html +=  '<span class="image-details">';

                        if (json[i]['expired']) {
                            html += '<span class="filename red-text">' + json[i]['filename'] + '***</span>';
                        } else if (json[i]['orphaned']) {
                            html += '<span class="filename purple-text">' + json[i]['filename'] + '*</span>';
                        } else {
                            html += '<span class="filename">' + json[i]['filename'] + '</span>';
                        }

                        html += '<br />' +
                                '<small>' + json[i]['size'] + '</small><br />' +
                                '<a class="button-select"><i class="fa fa-hand-o-up"></i> ' + buttonSelect + '</a>' +
                                '<input type="hidden" name="image" value="' + json[i]['file'] + '" />' +
                            '</span>' +
                        '</div>';
                    }
                }

                $('#my-images').html(html);
                // $('#my-images').trigger('scrollstop');
            },
            complete: function() {
                $('#my-images div > img').each(function(index, element) {
        			$(element).parent('div').css({'opacity': '0', 'visibility': 'visible'}).animate({'opacity': '1'}, 600, 'swing');
        		});
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    loadImages('');

	$('#my-images').clickOrTouch('.image-details', function(e) {
		e.preventDefault();

        $('.button-select').off('click touchstart');
        $('.button-select').html('<i class="fa fa-hand-o-up"></i> ' + buttonSelect);

        var $parentDiv = $(this).parent('div');
        var $buttonSelect = $(this).find('.button-select');

		if ($parentDiv.hasClass('selected')) {
			$parentDiv.removeClass('selected');
		} else {
			$('#my-images div').removeClass('selected');
			$parentDiv.addClass('selected');
            $buttonSelect.html('<i class="fa fa-check-circle"></i> ' + buttonApply);
            $buttonSelect.clickOrTouch(null, selectImage);
		}
	});

	$('#my-images').on('dblclick', 'div', function(e) {
		e.preventDefault();

		if (textEditor != false) {
    		// window.opener.CKEDITOR.tools.callFunction(textEditor, urlImages + $(this).find('input[name=\'image\']').val());
    		self.close();
		} else {
    		if (fieldTarget.length) {
                parent.$('#' + fieldTarget).val('data/' + imageDirectory + $(this).find('input[name=\'image\']').val());
            }

    		parent.$('#dialog').dialog('close');
    		parent.$('#dialog').remove();
		}
	});

    function selectImage(e) {
		e.preventDefault();

        var selected_path = $(this).next('input[name=\'image\']').val();

		if (textEditor != false) {
    		// window.opener.CKEDITOR.tools.callFunction(textEditor, urlImages + $(this).find('input[name=\'image\']').val());
    		self.close();
		} else {
    		if (fieldTarget.length) {
                parent.$('#' + fieldTarget).val('data/' + imageDirectory + selected_path);
            }

    		parent.$('#dialog').dialog('close');
    		parent.$('#dialog').remove();
		}
	}

	new AjaxUpload('#upload', {
		action: 'filemanager-upload',
		name: 'image[]',
		autoSubmit: false,
		responseType: 'json',
		onChange: function(file, extension) {
            this.setData({'directory': ''});
			this.submit();
		},
		onSubmit: function(file, extension) {
			$('#upload > i').attr('class', 'fa fa-spin fa-circle-o-notch icon-spin');
			$('#upload').prop('disabled', true);
		},
		onComplete: function(file, json) {
            var alert_msg = "";

			$('#upload > i').attr('class', 'fa fa-upload');
			$('#upload').prop('disabled', false);

			if (typeof json.success !== 'undefined') {
                loadImages('');
                alert(textSuccessImageUploaded);
			}

			if (json.error) {
				// alert(json.error.join(", \n")); // single upload
				j = 1;

				for (i = 0; i < json.error.length; i++) {
					if (json.error[i].length) {
						alert_msg += "#" + j + " - ";
						alert_msg += json.error[i].join(", \n");
						alert_msg += "\n\r";
						j++;
					}
				}

				if (alert_msg.length) {
					alert_msg = textWarning + "\n\r".concat(alert_msg);
					alert(alert_msg);
				}
			}

			//$('.loading').remove();
		}
	});

	$('#menu').clickOrTouch('#select', function(e) {
		e.preventDefault();

		var selected_path = $('#my-images div.selected').find('input[name=\'image\']').val();

		if (selected_path) {
			if (textEditor != false) {
    			// window.opener.CKEDITOR.tools.callFunction(textEditor, urlImages + selected_path);
    			self.close();
			} else {
                if (fieldTarget.length) {
                    parent.$('#' + fieldTarget).val('data/' + imageDirectory + selected_path);
                }

    			parent.$('#dialog').dialog('close');
    			parent.$('#dialog').remove();
			}
		}
	});

	$('#menu').on('click', '#refresh', function(e) {
		loadImages('');
	});

    $('#menu').on('click', '#rename', function(e) {
		if (!$('#my-images div.selected').length) {
            return alert(textErrorSelect);
        }

        var path = $('#my-images div.selected').first().find('input[name=\'image\']').attr('value');
        var currentFilename = path.substr(1).slice(0, -4);
        var newFilename = prompt('New Filename', currentFilename);

        if (newFilename == null || newFilename == '') {
			return false;
		} else {
			$.ajax({
				url: 'filemanager-rename',
				type: 'post',
				data: 'path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent(newFilename),
				dataType: 'json',
				success: function(json) {
					if (json.success) {
						alert(json.success);
                        loadImages('');
					}

					if (json.error) {
						alert(json.error);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
                    var msg = "Sorry, but there was an error: ";
                    $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + msg + xhr.status + " " + xhr.statusText + '</p></div></div>');
                    $('.widget-warning').fadeIn('slow');
                    //console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    // alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
			});
		}
	});

    $('#menu').on('click', '#delete', function(e) {
		if (!$('#my-images div.selected').length) {
            alert(textErrorSelect);
        } else if (!confirm(textConfirmDelete)) {
			return false;
		} else {
			$('#my-images div.selected').each(function() {
				var path = $(this).find('input[name=\'image\']').attr('value');

				$.ajax({
					url: 'filemanager-delete',
					type: 'post',
					data: 'path=' + encodeURIComponent(path),
					dataType: 'json',
					success: function(json) {
						if (json.success) {
							alert(json.success);
                            loadImages('');
						}

						if (json.error) {
							alert(json.error);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});
		}
	});

	$('#menu').clickOrTouch('#search-files-button', function(e) {
		e.preventDefault();

		var str = $("#search-files").val();
		var files_found = $("#my-images div:contains('" + str + "')");

		$('#my-images div').hide();

		if (files_found.length > 0) {
			files_found.each(function(index, element) {
				var height = $('#my-images').height();
				var offset = $(element).offset();

				if ((offset.top >= 0) && (offset.top < height)) {
					loadImage(element);
				}
			});

			files_found.fadeIn("slow");
		}
	});

	$('#menu').on('keydown', '#search-files', function(e) {
		if (e.keyCode == 13) {
			$('#search-files-button').trigger('click');
		}
	});
});
