if (typeof textWait === 'undefined') {
    var textWait = 'Please Wait';
}

if (typeof textMore === 'undefined') {
    var textMore = 'Load More';
}

if (typeof textView === 'undefined') {
    var textView = 'View Listing';
}

if (typeof textSave === 'undefined') {
    var textSave = 'Save:';
}

if (typeof textTax === 'undefined') {
    var textTax = 'Tax:';
}

if (typeof buttonQuickview === 'undefined') {
    var buttonQuickview = 'Quick View';
}

if (typeof buttonCart === 'undefined') {
    var buttonCart = 'Order';
}

if (typeof buttonContact === 'undefined') {
    var buttonContact = 'Contact';
}

if (typeof buttonWishlist === 'undefined') {
    var buttonWishlist = 'Save';
}

if (typeof buttonCompare === 'undefined') {
    var buttonCompare = 'Compare';
}

if (typeof textLoading === 'undefined') {
    var textLoading = 'Loading...';
}

$(document).ready(function() {
    /* Floating Sidebar */
    setTimeout(function() {
    	var $sidebarSelector = $('#sidebar');
    	var sidebarHeight = $sidebarSelector.outerHeight();
    	var contentHeight = $('#sidebar + .container-center > .content-page').outerHeight();

    	if ($sidebarSelector.length && sidebarHeight < contentHeight) {
            var previousScroll = 0;
    		var headerHeight = $('.container-header-top').outerHeight() + $('.container-header').outerHeight();
            var titleHeight = $('.container-page .breadcrumb').length ? $('.container-page .breadcrumb').outerHeight() : 0;

    		$(window).on('scroll resize touchmove', function(e) {
                var scrollTop = $(this).scrollTop();
                var windowWidth = $(this).width();
    			var viewportHeight = $(this).height();
    			var newContainerTopHeight = $('.container-top').length ? $('.container-top').outerHeight() : 0;
    			var newContentHeight = $('#sidebar + .container-center > .content-page').outerHeight();
                var newSidebarHeight = $sidebarSelector.outerHeight();
                var floatingHeaderHeight = $('.container-header.floating-menu .menu').length ? $('.container-header.floating-menu .menu').outerHeight() : 0;

                if (windowWidth > 979) {
                    var floatStart = headerHeight + titleHeight + newContainerTopHeight;
                    var floatStop = headerHeight + titleHeight + newContainerTopHeight + newContentHeight - viewportHeight;

                    // scroll starts later for large sidebar
                    if (newSidebarHeight >= viewportHeight) {
                        floatStart += newSidebarHeight - viewportHeight;
                    }

                    //console.log(scrollTop, floatStart, floatStop, newContentHeight, newSidebarHeight)

        			if (scrollTop >= floatStart && scrollTop < floatStop) {
                        var floatPosition = scrollTop - floatStart + 10;

                        // push below floating header for small sidebar on scroll up
                        if (scrollTop <= previousScroll && newSidebarHeight < viewportHeight) {
                            floatPosition += floatingHeaderHeight;
                        }

        				$sidebarSelector.stop().addClass('sidebar-floating').animate({
        					'top': floatPosition + 'px'
        				}, 600, 'swing');
        			} else if (scrollTop < floatStart) {
        				$sidebarSelector.removeClass('sidebar-floating').removeAttr('style');
        			}
                }

                previousScroll = scrollTop;
    		});
    	}
    }, 1000);

    // this eventually needs to be updated to build the element using JS versus setting innerHTML super-string
    function createListingElement(listing, isModule) {
        var elem = document.createElement('article');

        var innerHtml = '';
        var priceInnerHtml = '';
        var actionButton = '';
        var gridItemClass = 'grid-item item-cat item-more' + (isModule ? ' module-item' : '');
        var dataFilterClass = listing['price'] && listing['special'] ? 'sale' : 'category';
        var listingImage = listing['thumb'] || 'image/no_image.jpg';
        var listingDescription = listing['year'] != '0000' ? listing['year'] + ' ' : '';

        if (listing['price']) {
            if (listing['featured']) {
                gridItemClass += ' grid-item-promo';
            } else if (listing['special']) {
                gridItemClass += ' grid-item-sale';
            }

            if (!listing['special']) {
                priceInnerHtml += '<span class="price-top">' + listing['price'] + '</span>';
            } else {
                priceInnerHtml += '<span class="price-old">' + listing['price'] + '</span>';
                priceInnerHtml += '<span class="price-new">' + listing['special'] + '</span>';
                priceInnerHtml += '<i class="badges sale-badges" rel="tooltip" data-placement="top" data-original-title="' + textSave + ' ' + listing['savebadges'] + '">-' + listing['salebadges'] + '&#37;</i>';
            }

            if (listing['tax']) {
                priceInnerHtml += '<span class="price-tax">' + textTax + ' ' + listing['tax'] + '</span>';
            }
        }

        listingDescription += listing['manufacturer_id'] > 1 ? listing['manufacturer'] + ' ' : '';
        listingDescription += listing['model'] + ' ' +  listing['size'];

        if (listing['quantity'] < 0) {
            actionButton = '<a href="' + listing['href'] + '" rel="tooltip" data-placement="top" data-original-title="' + textView + '"><i class="fa fa-info-circle"></i></a>';
        } else if (listing['price'] && listing['quantity'] > 0) {
            actionButton = '<a onclick="addToCart(\'' + listing['product_id'] + '\');" rel="tooltip" data-placement="top" data-original-title="' + buttonCart + '"><i class="fa fa-shopping-cart"></i></a>';
        } else {
            actionButton = '<a onclick="messageProfile(\'' + listing['customer_id'] + '\', \'' + listing['product_id'] + '\')" rel="tooltip" data-placement="top" data-original-title="' + buttonContact + '"><i class="fa fa-envelope-o"></i></a>';
        }

        // innerHtml += '<article class="' + gridItemClass + '" data-filter-class="[\'' + dataFilterClass + '\']">';
        innerHtml += '   <div class="image">';
        innerHtml += '       <a href="' + listing['href'] + '" title="' + listing['name'] + '">';
        innerHtml += '           <img src="' + listingImage + '" alt="' + listing['name'] + '" />';
        innerHtml += '       </a>';
        innerHtml += '       <span class="description">';
        innerHtml += '           <a href="' + listing['href'] + '" title="' + listing['name'] + '">' + listingDescription + '</a>';
        innerHtml += '       </span>';
        innerHtml += '       <div class="quickview">';
        innerHtml += '           <a class="button button_quickview smaller" href="' + listing['quickview'] + '"  rel="quickview"><i class="fa fa-eye"></i> ' + buttonQuickview + '</a>';
        innerHtml += '       </div>';
        innerHtml += '   </div>';
        innerHtml += '   <div class="pannel">';
        innerHtml += '        <div class="info">';
        innerHtml += '           <header><h3><a href="' + listing['href'] + '" title="' + listing['name'] + '">' + listing['name'] + '</a></h3></header>';

        if (listing['price'] && listing['quantity'] >= 0) {
            innerHtml += '       <div class="price">' + priceInnerHtml + '</div>';
        }

        innerHtml += '       </div>';
        innerHtml += '       <footer class="add-to add-to111">';
        innerHtml += '           <a onclick="addToWishList(\'' + listing['product_id'] + '\');" rel="tooltip" data-placement="top" data-original-title="' + buttonWishlist + '"><i class="fa fa-save"></i></a>';
        innerHtml +=             actionButton;
        innerHtml += '           <a onclick="addToCompare(\'' + listing['product_id'] + '\');" rel="tooltip" data-placement="top" data-original-title="' + buttonCompare + '"><i class="fa fa-copy"></i></a>';
        innerHtml += '       </footer>';
        innerHtml += '   </div>';
        // innerHtml += '</article>';

        elem.className = gridItemClass;
        elem.setAttribute('data-filter-class', dataFilterClass);
        elem.innerHTML = innerHtml;

        return elem;
    }

    // Grid Items
    $('.grid').each(function() {
        var $thisGrid = $(this);
        var $progressBar = $('.progress-bar');
        var loadedImages = 0;
        var gridCount = $thisGrid.find('.grid-item').length || 0;
        var $loadMore = $thisGrid.siblings('.buttons').find('.load-more');
        var $masonryGrid = $thisGrid.masonry({
            itemSelector: '.grid-item',
            columnWidth: '.grid-sizer', //213,
            percentPosition: true,
            gutter: 4,
            transitionDuration: 0
        });

        $thisGrid.imagesLoaded(function() {
            $('.loading').fadeOut(300);
        }).always(function () {
            $progressBar.hide();
        }).progress(function (instance, image) {
            $(image.img).closest('.grid-item').css({
                'opacity': '0',
                'visibility': 'visible'
            }).animate({
                'opacity': '1'
            }, 300, 'swing');

            $progressBar.css('width', (++loadedImages / gridCount * 100) + '%');
            $masonryGrid.masonry('layout');
        });

        $('a.button_quickview').colorbox({
            rel: $(this).attr('rel'),
            opacity: 0.4,
            transition: "none",
            fixed: true,
            innerWidth: "100%",
            maxWidth: "100%",
            innerHeight: "80%",
            current: "",
            previous: "<i class='fa fa-chevron-left'></i>",
            next: "<i class='fa fa-chevron-right'></i>",
            close: "<i class='fa fa-times'></i>",
            onOpen: function() {
                $("#colorbox").css("opacity", 0);
            },
            onComplete: function() {
                $(this).resize();
                $("#colorbox").animate({"opacity": 1}, 300, 'swing');
            }
        });

        // Load more
        if ($loadMore.length) {
            $loadMore.clickOrTouch(null, function(e) {
                e.preventDefault();

                var url = $loadMore.attr('href');

                if (url.length && url.indexOf('/listings') !== -1) {
                    location = url;
                } else if (url.length && url.indexOf('more') !== -1) {
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        beforeSend: function() {
                            $loadMore.prop('disabled', true);
                            $loadMore.html('<i class="fa fa-spin fa-circle-o-notch icon-spin"></i> ' + textWait);
                        },
                        complete: function() {
                            $('.pagination').remove();
                            $('.item-more a.button_quickview').colorbox({
                                rel: $(this).attr('rel'),
                                opacity: 0.4,
                                transition: "none",
                                fixed: true,
                                innerWidth: "100%",
                                maxWidth: "100%",
                                innerHeight: "80%",
                                current: "",
                                previous: "<i class='fa fa-chevron-left'></i>",
                                next: "<i class='fa fa-chevron-right'></i>",
                                close: "<i class='fa fa-times'></i>",
                                onOpen: function() {
                                    $("#colorbox").css("opacity", 0);
                                },
                                onComplete: function() {
                                    $(this).resize();
                                    $("#colorbox").animate({"opacity": 1}, 300, 'swing');
                                }
                            });
                        },
                        success: function(json) {
                            // console.log(json)
                            var isModule = (typeof json['module'] !== 'undefined' && json['module'] === true);

                            if (json.listings.length) {
                                var i = 0;
                                var batch = 5;

                                while (i < json.listings.length) {
                                    var htmlBatch = [];

                                    for (var j = 0; j < batch && j <= json.listings.length - i; j++) {
                                        htmlBatch.push(createListingElement(json['listings'][i], isModule));
                                        i++;
                                    }

                                    var $batchListings = $(htmlBatch);

                                    $masonryGrid.append($batchListings).masonry('appended', $batchListings);

                                    $batchListings.imagesLoaded().progress(function (instance, image) {
                                        $masonryGrid.masonry('layout');

                                        $(image.img).closest('.grid-item').css({
                                            'opacity': '0',
                                            'visibility': 'visible'
                                        }).animate({
                                            'opacity': '1'
                                        }, 300, 'swing');
                                    });
                                }

                                if (json['more_href']) {
                                    $loadMore.prop('disabled', false);
                                    $loadMore.attr('href', decodeURIComponent(json['more_href']));
                                    $loadMore.html('<i class="fa fa-chevron-down"></i> ' + json['text_more']);
                                } else {
                                    $loadMore.remove();
                                }
                            } else {
                                $loadMore.before('<p><span class="error"><i class="fa fa-info-circle"></i> ' + json['text_none'] + '</span></p>');
                                $loadMore.remove();
                            }
                        },
                        error: handleError
                    });
                } else {
                    $loadMore.remove();
                }
            });
        }
    });

    // // Brand Filter
    // var $manufacturerFilter = $('#manufacturer-filter');
    //
    // $manufacturerFilter.selectmenu({
    // 	width: $('.product-filter .manufacturer-filter').width() - 1,
    // 	icons: [
    // 		{find: '.brand-logo'}
    // 	],
    // 	bgImage: function() {
    // 		return $(this).css("background-image");
    // 	}
    // });

    // // Category Filter
    // var $categoryFilter = $('#category-filter');
    //
    // if ($categoryFilter.length) {
    // 	$categoryFilter.selectmenu({
    // 		width: $('.product-filter .category-filter').width() - 1,
    // 		icons: [
    // 			{find: '.category-logo'}
    // 		],
    // 		bgImage: function() {
    // 			return $(this).css("background-image");
    // 		}
    // 	});
    //
    // 	$(window).resize(function() {
    // 		var select_width = $('.product-filter .category-filter').width() - 1;
    //
    // 		$categoryFilter.selectmenu({width: select_width});
    // 		// $('#manufacturer-filter').selectmenu({width: select_width});
    // 	});
    // }

    if ($('#listings').length) {
        var $progressBar = $('.progress-bar');
        var $listingItems = $('#listings > .listing-item');
        var totalImages = $('#listings .image').length;
        var loadedImages = 0;

        console.log(totalImages)

        $listingItems.hide();

        $('#listings').imagesLoaded(function() {
            $('.loading').fadeOut(200);

            var wookmark = new Wookmark('#listings', {
                autoResize: true,
                fillEmptySpace: true,
                align: 'center',
                container: $('#listings-container'),
                direction: 'left',
                resizeDelay: 50,
                offset: 10
            });

            var $filters = $('.filter-listings li');

            var onClickFilter = function(e) {
                var $item = $(e.currentTarget);
                var activeFilters = [];

                $item.toggleClass('active');

                $filters.filter('.active').each(function() {
                    activeFilters.push($(this).data('filter'));
                });

                wookmark.filter(activeFilters, 'or');
            }

            $filters.clickOrTouch(null, onClickFilter);
        }).always(function () {
            $progressBar.hide();
        }).progress(function (instance, image) {
            console.log(loadedImages / totalImages);
            $progressBar.css('width', (++loadedImages / totalImages * 100) + '%');
        }).done(function () {
            $listingItems.css({
                'opacity': '0',
                'visibility': 'visible'
            }).animate({
                'opacity': '1'
            }, 300, 'swing');
        });
    }

    // Lightbox
    if ($('.lightbox').length) {
        $('.lightbox').colorbox(colorboxDefault);
    }

    // Profile Filters
    if ($('#sidebar .profile-filter')) {
        $('#sidebar .profile-filter').find('input[type="checkbox"]').iCheck({
        	checkboxClass: 'icheckbox_minimal-custom',
        	radioClass: 'iradio_minimal-custom',
        	increaseArea: '20%' // optional
        });
    }

    // Brands and Profiles Name Filter
    $('.profile-filter,.brand-filter').on('keydown', 'input[name=\'filter_name\']', function(e) {
        if (e.keyCode == 13) {
            filterListings();
        }
    });

    function filterListings() {
        var url = $('input[name=\'reset_url\']').val();
        var filters = [];
        var filter_member = [];

        var filter_name = $('input[name=\'filter_name\']').val();
        var filter_category_id = $('select[name=\'filter_category_id\']').val();
        var filter_country_id = $('select[name=\'filter_country_id\']').val();
        var filter_zone_id = $('select[name=\'filter_zone_id\']').val();

        $('input[name=\'filter_member[]\']:checked').each(function() {
            filter_member.push($(this).val());
        });

        if (filter_name) {
            filters.push('filter_name=' + encodeURIComponent(filter_name));
        }

        if (filter_category_id > 0) {
            filters.push('&filter_category_id=' + encodeURIComponent(filter_category_id));
        }

    	if (filter_country_id >= 0) {
    		filters.push('filter_country_id=' + encodeURIComponent(filter_country_id));
    	}

    	if (filter_zone_id >= 0) {
    		filters.push('filter_zone_id=' + encodeURIComponent(filter_zone_id));
    	}

        if (filter_member.length) {
            filters.push('filter_member=' + encodeURIComponent(filter_member.join()));
        }

        if (filters.length) {
            url += '?' + filters.join('&');
        }

        location = url;
    }
});
