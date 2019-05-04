// Error handler
if (typeof handleError === 'undefined') {
    function handleError(xhr, ajaxOptions, thrownError) {
        var msg = "Sorry, but there was an error: ";
        $('#notification').html('<div class="bg"><span class="close"><i class="fa fa-times-circle-o"></i></span><div class="widget widget-warning" style="display: none;"><h6><i class="fa fa-exclamation-triangle"></i></h6><p>' + msg + xhr.status + " " + xhr.statusText + '</p></div></div>');
        $('.widget-warning').fadeIn('slow');
        //console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
    }
}

function filterCategoriesJSON(searchTerm, categoriesJson, minPath, exactMatch = false) {
    if (searchTerm.length) {
        return exactMatch ? categoriesJson.filter(function(obj) {
            return obj.name.toLowerCase() == searchTerm.toLowerCase() && obj.path.split("_").length >= minPath;
        }) : categoriesJson.filter(function(obj) {
            return obj.path_name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1 && obj.path.split("_").length >= minPath;
        });
    } else {
        return categoriesJson;
    }
}

function setCategoriesByPath(path) {
    var categories = path.split('_');

    switch (categories.length) {
        case 1:
        case 2:
        case 3:
            $('select[name=\'category_id\']').val(categories[0]).trigger('change');
        case 2:
        case 3:
            setTimeout(function() {
                $('select[name=\'sub_category_id\']').val(categories[1]).trigger('change');
            }, 300);
        case 3:
            setTimeout(function() {
                $('select[name=\'third_category_id\']').val(categories[2]);
            }, 600);
            break;
        default:
            // do nothing
    }
}

function getManufacturerOptionsByCategory(categoryObj, categoriesJson, manufacturersJson) {
    var html;
    var categoryTopParent;
    var selectedCategoryName = '';
    var selectedCategoryId = 0;
    var manufacturerIds = [];
    var manufacturers = manufacturersJson;
    var manufacturerId = $('select[name=\'manufacturer_id\']').val();
    var textSelectManufacturer = $('input[name=\'text_select_manufacturer\']').val();
    var textSelectManufacturerCount = $('input[name=\'text_select_manufacturer_count\']').val();
    var textManufacturerOther =  $('input[name=\'text_manufacturer_other\']').val();

    if (categoryObj.length) {
        selectedCategoryName = categoryObj[0].name;
        selectedCategoryId = categoryObj[0].id;
    }

    $('select[name=\'manufacturer_id\']').val('0').trigger('change');

    // updated so only top-level categories include list of manufacturers
    if (categoryObj.length && categoryObj[0].id != 0) {
        if (typeof categoryObj[0].manufacturer_ids !== 'undefined' && categoryObj[0].manufacturer_ids.length) {
            manufacturerIds = categoryObj[0].manufacturer_ids;
        } else if (categoriesJson.length) {
            categoryTopParent = categoriesJson.filter(function(obj) {
                return obj.id == categoryObj[0].path.split('_')[0];
            });

            if (categoryTopParent.length && typeof categoryTopParent[0].manufacturer_ids !== 'undefined' && categoryTopParent[0].manufacturer_ids.length) {
                manufacturerIds = categoryTopParent[0].manufacturer_ids;
            }
        }

        manufacturers = manufacturersJson.filter(function(item) {
            return manufacturerIds.indexOf(item.id) !== -1;
        });
    }

    if (manufacturers.length) {
        html = '<option value="">' + textSelectManufacturerCount.formatUnicorn({"category": selectedCategoryName, "count": manufacturers.length}) + '</option>';

        html += manufacturerId == '1'
            ? '<option value="1" selected="selected">' + textManufacturerOther + '</option>'
            : '<option value="1">' + textManufacturerOther + '</option>';

        for (i = 0; i < manufacturers.length; i++) {
            if (manufacturers[i]['id'] == '1') continue; // Other

            html += manufacturers[i]['id'] == manufacturerId
                ? '<option value="' + manufacturers[i]['id'] + '" selected="selected">' + manufacturers[i]['name'] + '</option>'
                : '<option value="' + manufacturers[i]['id'] + '">' + manufacturers[i]['name'] + '</option>';
        }

        return html;
    }

    // ajax fallback
    if (!manufacturersJson.length && !manufacturers.length) {
        $.ajax({
            url: 'manufactuer_category?category_id=' + selectedCategoryId,
            dataType: 'json',
            beforeSend: function() {
                $('select[name*=\'category\']').before('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
            },
            complete: function() {
                $('.wait').remove();
            },
            success: function(json) {
                var html = '<option value="0">' + textSelectManufacturer + '</option>';

                if (json['count']) {
                    // html = '<option value="0">' + json['text_select'] + '</option>';

                    for (i = 0; i < json['count']; i++) {
                        html += '<option value="' + json['manufacturer'][i]['manufacturer_id'] + '"';

                        if (json['manufacturer'][i]['manufacturer_id'] == manufacturerId) {
                            html += ' selected="selected"';
                        }

                        html += '>' + json['manufacturer'][i]['name'] + '</option>';
                    }
                }

                return html;
            },
            error: handleError
        });
    }
}

// Sub-Category and Manufacturer select (Search, Post)
(function() {
    $values = [];

    if ($('#searchwidget').length) {
        $values.push($('#searchwidget'));
    }

    if ($('#form.account-product-form').length) {
        $values.push($('#form.account-product-form'));
    }

    $.each($values, function(index, $value) {
        var categories_json = $('input[name=\'categories_complete\']').length ? JSON.parse($('input[name=\'categories_complete\']').val()) : [];
        var manufacturers_json = $('input[name=\'manufacturers_all\']').length ? JSON.parse($('input[name=\'manufacturers_all\']').val()) : [];
        var textSelectCategory = $('input[name=\'text_select_category\']').val();
        var textSelectCategorySub = $('input[name=\'text_select_category_sub\']').val();
        var textSelectCategoryCount =  $('input[name=\'text_select_category_count\']').val();
        var textSelectCategorySubCount = $('input[name=\'text_select_category_sub_count\']').val();

        // update sub-category list based on category selected
        $value.on('change', 'select[name=\'category_id\']:not(.no-cascade)', function() {
        	$('select[name=\'sub_category_id\'],select[name=\'third_category_id\'],select[name=\'manufacturer_id\']').prop('disabled', true);

            var subCategoryId = $('select[name=\'sub_category_id\']').val();
        	var selectedCategoryId = this.value;
        	var selectedCategory = categories_json.length ? categories_json.filter(function(obj) {
        		return obj.id == selectedCategoryId;
        	}) : [];
        	var subCategories = selectedCategory.length && selectedCategory[0].children !== undefined ? selectedCategory[0].children : [];

        	if (subCategories.length) {
        		// var htmlSubCategory = '<option value="">' + textSelectCategorySub + '</option>';
        		var htmlSubCategory = '<option value="">' + textSelectCategorySubCount.formatUnicorn({"count": subCategories.length}) + '</option>';

        		for (i = 0; i < subCategories.length; i++) {
        			htmlSubCategory += subCategories[i]['category_id'] == subCategoryId
        				? '<option value="' + subCategories[i]['category_id'] + '" selected="selected">' + subCategories[i]['name'] + '</option>'
        				: '<option value="' + subCategories[i]['category_id'] + '">' + subCategories[i]['name'] + '</option>';
        		}

        		$('select[name=\'sub_category_id\']').prop('disabled', false);
        		$('select[name=\'sub_category_id\']').html(htmlSubCategory);
                $('.sub-category-wrapper').slideDown(300);
        	} else {
                $('.third-category-wrapper').slideUp(300);
            }

            var htmlManufacturer = getManufacturerOptionsByCategory(selectedCategory, categories_json, manufacturers_json);

            $('select[name=\'manufacturer_id\']').prop('disabled', false);
            $('select[name=\'manufacturer_id\']').html(htmlManufacturer);

        	// ajax fallback
        	if (!categories_json.length) {
        		$.ajax({
        			url: 'sub_category?category_id=' + this.value,
        			dataType: 'json',
        			beforeSend: function() {
        				$('select[name=\'category_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
        			},
        			complete: function() {
        				$('.wait').remove();
        			},
        			success: function(json) {
        				html = '<option value="">' + textSelectCategorySub + '</option>';

        				if (json['count']) {
        					html = '<option value="">' + json['text_select'] + '</option>';

        					for (i = 0; i < json['count']; i++) {
        						html += '<option value="' + json['category'][i]['category_id'] + '"';

        						if (json['category'][i]['category_id'] == subCategoryId) {
        							html += ' selected="selected"';
        						}

        						html += '>' + json['category'][i]['name'] + '</option>';
        					}

        					$('select[name=\'sub_category_id\']').prop('disabled', false);
                            $('select[name=\'sub_category_id\']').html(html);
                            $('.sub-category-wrapper').slideDown(300);
        				}
        			},
        			error: handleError
        		});
        	}
        });

        // update third-category list based on sub-category selected
        $value.on('change', 'select[name=\'sub_category_id\']', function() {
        	$('select[name=\'third_category_id\'],select[name=\'manufacturer_id\']').prop('disabled', true);

            var thirdCategoryId = $('select[name=\'third_category_id\']').val();
        	var selectedCategoryId = $('select[name=\'category_id\']').val();
        	var selectedCategory = categories_json.length ? categories_json.filter(function(obj) {
        		return obj.id == selectedCategoryId;
        	}) : [];
        	var selectedSubCategoryId = this.value;
        	var selectedSubCategory = categories_json.length ? categories_json.filter(function(obj) {
        		return obj.parent_id == selectedCategoryId && obj.id == selectedSubCategoryId;
        	}) : [];
        	var thirdCategories = selectedSubCategory.length && selectedSubCategory[0].children !== undefined ? selectedSubCategory[0].children : [];
        	var htmlThirdCategory = '<option value="">' + textSelectCategorySub + '</option>';

        	if (thirdCategories.length) {
        		htmlThirdCategory = '<option value="">' + textSelectCategorySubCount.formatUnicorn({"count": thirdCategories.length}) + '</option>';

        		for (i = 0; i < thirdCategories.length; i++) {
        			htmlThirdCategory += '<option value="' + thirdCategories[i]['category_id'] + '"';

        			if (thirdCategories[i]['category_id'] == thirdCategoryId) {
        				htmlThirdCategory += ' selected="selected"';
        			}

        			htmlThirdCategory += '>' + thirdCategories[i]['name'] + '</option>';
        		}

        		$('select[name=\'third_category_id\']').prop('disabled', false);
        		$('select[name=\'third_category_id\']').html(htmlThirdCategory);
        		$('.third-category-wrapper').slideDown(300);
        	} else {
                $('.third-category-wrapper').slideUp(300);
            }

        	var htmlManufacturer = selectedSubCategory.length
                ? getManufacturerOptionsByCategory(selectedSubCategory, categories_json, manufacturers_json)
                : getManufacturerOptionsByCategory(selectedCategory, categories_json, manufacturers_json);

            $('select[name=\'manufacturer_id\']').prop('disabled', false);
            $('select[name=\'manufacturer_id\']').html(htmlManufacturer);

        	// ajax fallback
        	if (!categories_json.length) {
        		$.ajax({
        			url: 'sub_category?category_id=' + this.value,
        			dataType: 'json',
        			beforeSend: function() {
        				$('select[name=\'sub_category_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
        			},
        			complete: function() {
        				$('.wait').remove();
        			},
        			success: function(json) {
        				html = '<option value="">' + textSelectCategorySub + '</option>';

        				if (json['count']) {
        					html = '<option value="">' + json['text_select'] + '</option>';

        					for (i = 0; i < json['count']; i++) {
        	        			html += '<option value="' + json['category'][i]['category_id'] + '"';

        						if (json['category'][i]['category_id'] == thirdCategoryId) {
        		      				html += ' selected="selected"';
        		    			}

        		    			html += '>' + json['category'][i]['name'] + '</option>';
        					}

        					$('select[name=\'third_category_id\']').prop('disabled', false);
                            $('select[name=\'third_category_id\']').html(html);
        					$('.third-category-wrapper').slideDown(300);
        				}
        			},
        			error: handleError
        		});
        	}

        });

        // update zone list based on country selected
        $value.on('change', 'select[name=\'country_id\']', function() {
            var country_id = this.value; // $(this).val();
            var zoneId = $('select[name=\'zone_id\']').val() || '0';

            if (country_id > 0) {
                $('select[name=\'zone_id\']').prop('disabled', false);
            } else {
                $('select[name=\'zone_id\']').prop('disabled', true);
            }

        	$.ajax({
        		url: 'country?country_id=' + this.value,
        		dataType: 'json',
        		beforeSend: function() {
        			$('select[name=\'country_id\']').after('<span class="wait icon"><i class="fa fa-spin fa-circle-o-notch icon-spin"></i></span>');
        			$('select[name=\'zone_id\']').prop('disabled', true);
        		},
        		complete: function() {
        			$('.wait').remove();
        			$('select[name=\'zone_id\']').prop('disabled', false);
        		},
        		success: function(json) {
                    var zones = false;
                    var textSelectZone = $('input[name=\'text_select_zone\']').val();

                    html = '<option value="">' + textSelectZone + '</option>';

                    if (typeof json['zone'] !== 'undefined' && json['zone'] != '') {
                        for (i = 0; i < json['zone'].length; i++) {
                            html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                            if (json['zone'][i]['zone_id'] == zoneId) {
                                html += ' selected="selected"';
                            }

                            html += '>' + json['zone'][i]['name'] + '</option>';
                        }

                        if (json['zone'].length) {
                            zones = true;
                            $('.location-wrapper').slideDown(300);
                        }
                    }

                    if (!zones) {
                        $('.location-wrapper').slideUp(300);
                    }

                    $('select[name=\'zone_id\']').html(html);
                    $('input[name=\'location\']').val('');
        		},
        		error: handleError
        	});
        });

    }); // END $.each
})();

// Accordion
$('.widget-ac-container').each(function() {
    $(this).clickOrTouch('.widget-ac > .ac-title,.widget-ac > h6', function(e) {
        var $headingTrigger = $(this);

        $headingTrigger.next('.ac-content').slideToggle('normal', function() {
            $headingTrigger.toggleClass('widget-ac-active');
        });
    });
});

// Filter Accordion
if (($('.widget-ac-container.collapse-medium .widget-ac').length && $(window).width() <= 979) || ($('.widget-ac-container.collapse-small .widget-ac').length) && $(window).width() <= 698) {
    $('.widget-ac-container .widget-ac').find('h6').toggleClass('widget-ac-active');
    $('.widget-ac-container .widget-ac').find('.ac-content').hide();
}

// Button More (Filter, Search, AnonPost)
$('#button-more-options').clickOrTouch(null, function(e) {
    e.preventDefault();

    var $slide_trigger = $(this);
    var text_hide_options = $(this).closest('.widget').find('input[name=\'text_hide_options\']').val();
    var text_more_options = $(this).closest('.widget').find('input[name=\'text_more_options\']').val();

    $('#container-more-options').slideToggle('slow', function() {
        $slide_trigger.toggleClass('active');

        if ($slide_trigger.hasClass('active')) {
            $slide_trigger.html('<i class="fa fa-minus-circle"></i>' + text_hide_options);
        } else {
            $slide_trigger.html('<i class="fa fa-plus-circle"></i>' + text_more_options);
        }
    });
});

// Slideshow, Banner, & Carousel
$(document).ready(function() {
    // jQuery.fn.addBack = jQuery.fn.andSelf;

    $('.widget-slideshow').each(function() {
        var $thisSlideshow = $(this);
        var slideshow_options = $thisSlideshow.find('input[name=\'slideshow_options\']').val();
        var sliderOptions = slideshow_options.length ? JSON.parse(slideshow_options) : {
            infinite: true,
            arrows: true,
            dots: false,
            fade: true,
            autoplay: true,
            autoplayTimeout: 5000,
            pauseOnHover: false,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<a class="slideshow-prev"><i class="fa fa-angle-left"></i></a>',
            nextArrow: '<a class="slideshow-next"><i class="fa fa-angle-right"></i></a>',
            zIndex: 198
        };

        sliderOptions.lazyLoad = 'progressive'; // 'ondemand';
        // sliderOptions.centerMode = true;
        // sliderOptions.centerPadding = '0';

        // Disable Homepage slider on mobile
        if ($thisSlideshow.parents('.home-page') && ($(window).width() <= 979 || typeof isMobile !== 'undefined' && isMobile.any())) {
            var homeBgImgSrc = $('.slideshow-item:first-child img').attr('src') || $('.slideshow-item:first-child img').attr('data-lazy');

            $thisSlideshow.html('');
            $thisSlideshow.css({
                'background-image': 'url(' + homeBgImgSrc + ')',
                'background-repeat': 'no-repeat',
                'background-position': 'right top'
            });
        } else {
            $thisSlideshow.find('.slideshow-carousel').slick(sliderOptions);

            $thisSlideshow.imagesLoaded(function() {
                $thisSlideshow.find('.loader').fadeOut(200);
                $thisSlideshow.addClass('widget-shadow');

                $('.home-page .widget-home').on('change', 'select[name=\'category\']', function() {
                    var categorySelectIndex = $(this).prop('selectedIndex');
                    var categorySelectName = $('option:selected', this).text().toLowerCase();

                    if (categorySelectIndex) {
                        $thisSlideshow.find('.slideshow-carousel').slick('slickGoTo', parseInt(categorySelectIndex) - 1);
                    } else {
                        categorySelectName = '';
                    }

                    $('.home-page .widget-home input[name=\'category_name\']').val(categorySelectName);
                });
            });
        }
    });

    $('.widget-banner').each(function() {
        var cycle_options = $(this).find('input[name=\'cycle_options\']').val();
        var sliderOptions = cycle_options.length ? JSON.parse(cycle_options) : null;

        $(this).find('.banner').cycle(sliderOptions);
    });

    $('.widget-carousel').each(function() {
        var carouselLimit = $(this).attr('data-limit') || 1;
        var carouselScoll = $(this).attr('data-scroll') || 1;

        $(this).find('ul').jcarousel({
            buttonNextHTML: '<div><i class="fa fa-angle-right"></i></div>',
            buttonPrevHTML: '<div><i class="fa fa-angle-left"></i></div>',
            vertical: false,
            visible: carouselLimit,
            scroll: carouselScoll,
            wrap: 'both',
            itemFallbackDimension: 250
        });
    });

    if ($('#container-more-options').attr('data-show') == 'true' && $('#container-more-options').css('display') == 'none') {
        $('#button-more-options').trigger('click');
    }

    if ($('input[name=\'category_name\']').length && $.isFunction($.fn.autocomplete) && typeof filterCategoriesJSON === 'function') {
        var categories_json = $('input[name=\'categories_complete\']').length ? JSON.parse($('input[name=\'categories_complete\']').val()) : [];
        var manufacturers_json = $('input[name=\'manufacturers_all\']').length ? JSON.parse($('input[name=\'manufacturers_all\']').val()) : [];
        var category_name = $('input[name=\'category_name\']').val();
        var is_mobile = (typeof isMobile !== 'undefined' && isMobile.any() && $(window).width() <= 446);

        if (category_name.length) {
            var categories = filterCategoriesJSON(category_name, categories_json, 2, true);

            if (categories.length && categories[0].path !== undefined) {
                setCategoriesByPath(categories[0].path);
            }
        }

        $('input[name=\'category_name\']').autocomplete({
            delay: 0,
            minLength: 0,
            maxLength: 10,
            source: function(request, response) {
                var categories = categories_json.length ? filterCategoriesJSON(request.term, categories_json, 2) : [];

                categories.sort(function(a,b) {
                    return (a.order > b.order) ? 1 : ((b.order > a.order) ? -1 : 0)
                });

                var results = $.map(categories, function(item) {
                    return {
                        label: item.path_name,
                        value: item.id,
                        image: item.image.length ? 'image/' + item.image : ''
                    }
                });

                response(results.slice(0, (is_mobile ? 6 : 12)));
            },
            change: function(event, ui) {
                if (ui.item == null || ui.item == undefined) {
                    $('input[name=\'category_name\']').val('');
                    $('select[name=\'category_id\']').val('0').trigger('change');
                    $('select[name=\'sub_category_id\']').val('0').trigger('change');
                    $('select[name=\'third_category_id\']').val('0').trigger('change');
                    $('select[name=\'manufacturer_id\']').val('0').trigger('change');
                }
            },
            select: function(event, ui) {
                var categoryNameSelected = ui.item.label.split('&raquo;').pop().trim();
                categoryNameSelected = $('<textarea />').html(categoryNameSelected).text();
                $('input[name=\'category_name\']').val(categoryNameSelected);

                var selectedCategoryId = ui.item.value;
                var selectedCategory = categories_json.length ? categories_json.filter(function(obj) {
                    return obj.id == selectedCategoryId;
                }) : [];
                var selectedCategoryPath = selectedCategory.length && selectedCategory[0].path !== undefined ? selectedCategory[0].path : '';

                setCategoriesByPath(selectedCategoryPath);

                var htmlManufacturer = getManufacturerOptionsByCategory(selectedCategory, categories_json, manufacturers_json);

                $('select[name=\'manufacturer_id\']').html(htmlManufacturer);

                return false;
            }
        }).focus(function () {
            $(this).autocomplete('search', $(this).val());
        }).autocomplete("instance")._renderItem = function(ul, item) {
            var thisSpanAttr = {
                style: 'background-image: url(' + item.image + ');background-size: contain;',
                class: 'ui-selectmenu-item-icon ui-icon'
            };
            var thisSpan = $('<span></span>', thisSpanAttr);
            var thisAAttr = {
                title: item.label,
                // href : '#nogo',
                tabindex : -1,
                role : 'option'
            };
            var thisA = $('<a></a>', thisAAttr)
                .append(thisSpan)
                .append(item.label)
                .removeClass('ui-corner-all')
                .on('focus,mouseover', function() {
                    $(this).parent().mouseover();
                })
                .on('blur,mouseout', function() {
                    $(this).parent().mouseout();
                });
            var thisLi = $('<li></li>')
                .append(thisA)
                .attr('role', 'presentation')
                .removeClass()
                .addClass('category-logo ui-selectmenu-hasIcon')
                .data('item.autocomplete', item)
                .on('mouseover', function() {
                    $(this).removeClass('ui-state-active').addClass('ui-selectmenu-item-focus ui-state-hover');
                    $(this).children().removeClass();
                })
                .on('mouseout', function() {
                    $(this).removeClass('ui-selectmenu-item-focus ui-state-hover');
                    $(this).children().removeClass();
                });

            ul.addClass('ui-selectmenu-menu').removeClass('ui-corner-all');

            return thisLi.appendTo(ul);
        };
    }
});

// All AJAX Select Dependencies
$(window).on('load', function() {
    var categoryId = $('select[name=\'category_id\']').val();
    var subCategoryId = $('select[name=\'sub_category_id\']').val();
    var manufacturerId = $('select[name=\'manufacturer_id\']').val();

    if (typeof categoryId != 'undefined' && categoryId !== '0') {
        $('select[name=\'category_id\']').trigger('change');

        if (typeof subCategoryId != 'undefined' && subCategoryId !== '0') {
            $('select[name=\'sub_category_id\']').trigger('change');
        }
    }
    // brand logo
    if ($('#form.account-product-form').length && manufacturerId > 1) {
        $('select[name=\'manufacturer_id\']').trigger('change');
    }
});
