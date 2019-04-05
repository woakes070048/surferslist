// search.js

function getParamValue(obj) {
    return obj.value;
}

function getParamName(obj) {
    return obj.name;
}

function getSearchParameters() {
    var searchParams = {};

    searchParams.filter = new Array();
    searchParams.condition = new Array();
    searchParams.type = new Array();
    searchParams.member = new Array();
    searchParams.forSale = false;

    var $fieldSearch = $('#searchwidget input[name=\'search\']');
    var $fieldTag = $('#searchwidget input[name=\'tag\']');
    var $fieldDescription = $('#searchwidget input[name=\'description\']:checked');
    var $fieldForSale = $('#searchwidget input[name=\'forsale\']:checked');
    var $fieldCategoryId = $('#searchwidget select[name=\'category_id\']');
    var $fieldSubCategoryId = $('#searchwidget select[name=\'sub_category_id\']');
    var $fieldThirdCategoryId = $('#searchwidget select[name=\'third_category_id\']');
    var $fieldBrand = $('#searchwidget select[name=\'manufacturer_id\']');
    var $fieldPrice = $('#searchwidget select[name=\'price\']');
    var $fieldAge = $('#searchwidget select[name=\'age\']');
    var $fieldCountry = $('#searchwidget select[name=\'country_id\']').length ? $('#searchwidget select[name=\'country_id\']') : $('#searchwidget input[name=\'country_id\']');
    var $fieldZone = $('#searchwidget select[name=\'zone_id\']').length ? $('#searchwidget select[name=\'zone_id\']') : $('#searchwidget input[name=\'zone_id\']');
    var $fieldLocation = $('#searchwidget input[name=\'location\']');
    var $fieldCondition = $('#searchwidget input[name=\'condition[]\']:checked');
    var $fieldType = $('#searchwidget input[name=\'type[]\']:checked');
    var $fieldMember = $('#searchwidget input[name=\'member[]\']:checked');

    searchParams.search = {
        value: $fieldSearch.val(),
        name: $fieldSearch.val()
    }

    searchParams.tag = {
        value: $fieldTag.val(),
        name: $fieldTag.val()
    }

    searchParams.description = $fieldDescription.length ? {
        value: $fieldDescription.val(),
        name: 'Yes'
    } : {
        value: false,
        name: 'No'
    }

    searchParams.category = {
        value: $fieldCategoryId.val(),
        name: $fieldCategoryId.find(':selected').text()
    }

    searchParams.category_sub = {
        value: $fieldSubCategoryId.val(),
        name: $fieldSubCategoryId.find(':selected').text()
    }

    searchParams.category_third = {
        value: $fieldThirdCategoryId.val(),
        name: $fieldThirdCategoryId.find(':selected').text()
    }

    searchParams.brand = {
        value: $fieldBrand.val(),
        name: $fieldBrand.find(':selected').text()
    }

    searchParams.price = {
        value: $fieldPrice.val(),
        name: $fieldPrice.find(':selected').text()
    }

    searchParams.age = {
        value: $fieldAge.val(),
        name: $fieldAge.find(':selected').text()
    }

    searchParams.country = {
        value: $fieldCountry.val(),
        name: $fieldCountry.find(':selected').text()
    }

    searchParams.zone = {
        value: $fieldZone.val(),
        name: $fieldZone.find(':selected').text()
    }

    searchParams.location = {
        value: $fieldLocation.val(),
        name: $fieldLocation.val()
    }

    $fieldCondition.each(function() {
        searchParams.condition.push({
            value: $(this).val(),
            name: $(this).closest('.label').find('label').text()
        });

        searchParams.filter.push($(this).val());
    });

    if ($fieldType.length) {
        $fieldType.each(function(el) {
            searchParams.type.push({
                value: $(this).val(),
                name: $(this).closest('.label').find('label').text()
            });
        });
    } else if ($fieldForSale.length) {
        searchParams.type.push({
            value: 0,
            name: $('label[for="type-0"]').text()
        }, {
            value: 1,
            name: $('label[for="type-1"]').text()
        });
    }

    searchParams.forSale = !$fieldType.length && $fieldForSale.length ? {
        value: $fieldForSale.val(),
        name: 'Yes'
    } : {
        value: false,
        name: 'No'
    }

    $fieldMember.each(function() {
        searchParams.member.push({
            value: $(this).val(),
            name: $(this).closest('.label').find('label').text()
        });
    });

    if (searchParams.price.value > 0) {
       searchParams.filter.push(searchParams.price.value);
    }

    if (searchParams.age.value > 0) {
       searchParams.filter.push(searchParams.age.value);
    }

    return searchParams;
}

function submitSearchForm(e) {
    e.preventDefault();

    var url = $('#searchwidget').attr('action');
    var searchParams = getSearchParameters();
    var search = [];

    if (searchParams.search.value) {
        search.push('s=' + encodeURIComponent(searchParams.search.value));
    }

    if (searchParams.tag.value) {
        search.push('tag=' + encodeURIComponent(searchParams.tag.value));
    }

    if (searchParams.description.value) {
        search.push('description=true');
    }

    if (searchParams.category.value > 0) {
        var path = searchParams.category.value;

        if (searchParams.category_sub.value > 0 && searchParams.category_sub.value != searchParams.category.value) {
            path += '_' + searchParams.category_sub.value;
        }

        if (searchParams.category_third.value > 0 && searchParams.category_third.value != searchParams.category.value) {
            path += '_' + searchParams.category_third.value;
        }

        search.push('category=' + encodeURIComponent(path));
    }

    if (searchParams.brand.value > 0) {
        search.push('brand=' + encodeURIComponent(searchParams.brand.value));
    }

    if (searchParams.filter.length) {
        search.push('filter=' + encodeURIComponent(searchParams.filter.join()));
    }

    if (searchParams.type.length) {
        search.push('type=' + encodeURIComponent(searchParams.type.map(getParamValue).join()));
    }

    if (searchParams.member.length) {
        search.push('member=' + encodeURIComponent(searchParams.member.map(getParamValue).join()));
    }

    if (searchParams.country.value > 0) {
        search.push('country=' + encodeURIComponent(searchParams.country.value));
    }

    if (searchParams.zone.value > 0) {
        search.push('state=' + encodeURIComponent(searchParams.zone.value));
    }

    if (searchParams.location.value) {
        search.push('location=' + encodeURIComponent(searchParams.location.value));
    }

    if (searchParams.forSale.value) {
        search.push('forsale=true');
    }

    if (search.length) {
        url += url.indexOf('?') !== -1 ? '&' : '?';
        url += search.join('&');
    }

    location = url;
}

(function() {
    var $searchWidget = $('#searchwidget');
    var $paramValues = $('.parameters').length ? $('.param-value') : $();
    var searchParamsObj = getSearchParameters();

    $paramValues.each(function() {
        var paramField = $(this).attr('data-field');
        var paramValue = $(this).attr('data-value');

        if (typeof searchParamsObj[paramField] !== 'undefined') {
            if (!Array.isArray(searchParamsObj[paramField])) {
                if (paramField == 'category') {
                    var categoryName = searchParamsObj.category_third.value > 0
                        ? searchParamsObj.category_third.name
                        : (searchParamsObj.category_sub.value > 0
                            ? searchParamsObj.category_sub.name
                            : searchParamsObj.category.name
                        );

                    $(this).html(categoryName);
                } else {
                    $(this).html(searchParamsObj[paramField].name);
                }
            } else {
                $(this).html(searchParamsObj[paramField].map(getParamName).join(', '));
            }
        }
    });

    $searchWidget.on('keydown', 'input[name=\'search\']', function(e) {
    	if (e.keyCode == 13) {
    		submitSearchForm(e);
    	}
    });

    $searchWidget.on('submit', submitSearchForm);

    //$searchWidget.clickOrTouch('#button-search', submitSearchForm);

    $searchWidget.clickOrTouch('#button-browse', function(e) {
        e.preventDefault();

    	var url;

    	var category_id = $('#searchwidget select[name=\'category_id\']').val();

    	if (category_id > 0) {
    		url = $('#searchwidget select[name=\'category_id\']').find(':selected').attr('data-url');
    	} else {
            url = $('#searchwidget input[name=\'products_page\']').val();
        }

    	var manufacturer_id = $('#searchwidget select[name=\'manufacturer_id\']').val();

    	if (manufacturer_id > 0) {
    		url += '?filter_manufacturer_id=' + encodeURIComponent(manufacturer_id);
    	}

    	location = url;
    });

    $searchWidget.on('change', 'select[name=\'zone_id\']', function() {
    	var zone_id = $(this).val();

        if (zone_id > 0) {
            $('#searchwidget input[name=\'location\']').prop('disabled', false);
            $('#searchwidget input[name=\'location\']').attr('placeholder', '');
        } else {
            $('#searchwidget input[name=\'location\']').prop('disabled', true);
            $('#searchwidget input[name=\'location\']').attr('placeholder', 'N/A');
        }

        $('#searchwidget input[name=\'location\']').val('');
    });

	$searchWidget.find('input[type="checkbox"]').iCheck({
		checkboxClass: 'icheckbox_minimal-custom',
		radioClass: 'iradio_minimal-custom',
		increaseArea: '20%' // optional
	});
})();

$(window).on('load', function() {
    window.setTimeout(function() {
        $('#searchwidget').on('change', 'select[name=\'category_id\']', function() {
            if (!$('#button-more-options').hasClass('active')) {
                $('#button-more-options').trigger('click');
            }
        });
    }, 900)
});
