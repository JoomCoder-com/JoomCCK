/**
 * Created by Sergey on 1/17/14.
 */
"use strict";
(function ($) {
    var defaults = {

    };

    function Geo(options, context) {
        this.options = $.extend({}, defaults, options);

        google.maps.visualRefresh = true;

        var latlng = new google.maps.LatLng(parseFloat(options.lat || options.plat), parseFloat(options.lng || options.plng));
        var zoom = parseInt(options.zoom || options.pzoom);

        var map = new google.maps.Map(context[0], {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoom: zoom,
            center: latlng,
            styles: options.style
        });

        var marker = new google.maps.Marker({
            'position': latlng,
            'map': map,
            'cursor': 'move',
            'visible': false,
            'draggable': true
        });
        setMarker(options.marker, parseInt(options.marker_w), parseInt(options.marker_h));

        var geocoder = new google.maps.Geocoder();
        var autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete' + options.id)));

        google.maps.event.addListener(map, 'click', function (event) {
            reset();
            setHiddenPosition(event.latLng);
        });

        google.maps.event.addListener(map, 'zoom_changed', function (event) {
            reset();
            $('#f' + options.id + '_position_zoom').val(map.getZoom());
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            reset();
            setHiddenPosition(event.latLng);
        });

        google.maps.event.addListener(autocomplete, 'place_changed', onPlaceChanged);

        initPosition();

        autocomplete.setBounds(new google.maps.LatLngBounds(map.getCenter(), map.getCenter()));

        var gl;
        try {
            gl = navigator.geolocation;
        } catch (e) {
        }

        if (!gl) {
            $('#cur_loc' + options.id).css('display', 'none');
        }

        function onPlaceChanged() {
            var place = autocomplete.getPlace();
            if (place.geometry) {
                setHiddenPosition(place.geometry.location);
                map.setZoom(17);
            } else {
                //document.getElementById('autocomplete').placeholder = 'Enter a city';
            }
        }

        function mapToCountry(country, city) {

            if (typeof country == 'undefined') {
                return;
            }

            var code_ = {
                'address': country + (typeof city == 'undefined' ? '': ', ' + city)
            };

            if (country.length == 2) {
                geocoder.geocode(code_, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        setHiddenPosition(results[0].geometry.location);
                        map.fitBounds(results[0].geometry.bounds);
                        autocomplete.setComponentRestrictions({ 'country': country.toLowerCase() });
                        $('#autocomplete' + options.id).val('');
                    }
                });
            }
        }

        function initPosition() {
            if (!options.lat && !options.lng) {
                var country = $('#f' + options.id + '_address_country').val() || '';


                if (country.length == 2) {
                    mapToCountry(country.toLowerCase());
                } else if (options.defaultcountry.length == 2) {
                    mapToCountry(options.defaultcountry.toLowerCase());
                } else if (options.initposition) {
                    $.ajax('http://ip-api.com/json/',{
                        dataType: "json"
                    }).done(function(result){
                        if(!result){
                            return;
                        }

                        if(result.status == 'fail') {
                            return;
                        }

                        if(result.lat && result.lon){
                            var latlong = new google.maps.LatLng(result.lat, result.lon);
                            setHiddenPosition(latlong);
                            map.setZoom(8);
                        } else if(result.countryCode){
                            mapToCountry(result.countryCode.toLowerCase(), result.city);
                        }
                    });
                }
            }
        }

        function setMarker(file, width, height) {

            if ($('#f' + options.id + '_position_lat').val() && $('#f' + options.id + '_position_lng').val()) {
                $('#rmp' + options.id).show();
                marker.setVisible(true);
            }
            marker.setIcon(new google.maps.MarkerImage(
                options.marker_path + file,
                new google.maps.Size(width, height),
                new google.maps.Point(0, 0),
                new google.maps.Point(width / 2, height)
            ));
            map.setCenter(marker.getPosition());
            $('#f' + options.id + '_position_marker').val(file);
        }

        function loader(name) {
            reset();
            $('#' + name + options.id)
                .attr('disabled', 'disabled')
                .css('background', 'url("' + options.root + '/media/com_joomcck/js/mooupload/imgs/load_bg_blue.gif")');
        }

        function reset() {
            $('#cur_loc' + options.id).removeAttr('disabled').css('background', '');
            $('#adr_loc' + options.id).removeAttr('disabled').css('background', '');
            $('#toadr_loc' + options.id).removeAttr('disabled').css('background', '');
        }

        function setHiddenPosition(latlong) {
            $('#f' + options.id + '_position_lat').val(latlong.lat());
            $('#f' + options.id + '_position_lng').val(latlong.lng());
            $('#f' + options.id + '_position_zoom').val(map.getZoom());
            marker.setVisible(true);
            marker.setPosition(latlong);
            map.panTo(latlong);
            $('#rmp' + options.id).show();
        }

        function displayPosition(position) {
            var latlong = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            marker.setPosition(latlong);
            map.setCenter(latlong);
            map.setZoom(15);
            setHiddenPosition(latlong);
            reset();
        }

        function displayError(error) {
            switch (error.code) {
                case error.TIMEOUT:
                    doFallback();
                    gl.getCurrentPosition(displayPosition, displayError);
                    break;
            }
            ;
            alert(error.message);
            reset();
            $('#cur_loc' + options.id).css('display', 'none');
        }

        function updatemap() {
            var lat = $('#f' + options.id + '_position_lat').val();
            var lng = $('#f' + options.id + '_position_lng').val();
            if (lat && lng) {
                var latlong = new google.maps.LatLng(lat, lng);
                marker.setVisible(true);
                marker.setPosition(latlong);
                map.panTo(latlong);
                map.setZoom(9);
                $('#rmp' + options.id).show();
            }
        }

        $('#f' + options.id + '_address_country').bind('change keyup', function () {
            initPosition($(this).val());
        });


        $('#f' + options.id + '_position_lat').bind('keyup blur', function () {
            updatemap();
        });

        $('#f' + options.id + '_position_lng').bind('keyup blur', function () {
            updatemap();
        });

        $('#rmp' + options.id).click(function () {
            $(this).hide();
            $('#f' + options.id + '_position_lat').val('');
            $('#f' + options.id + '_position_lng').val('');
            marker.setVisible(false);
        });

        $('img[data-field-id="' + options.id + '"]').click(function () {
            var img = $(this);
            setMarker(img.data('marker-file'), img.data('marker-width'), img.data('marker-height'));
            setHiddenPosition(marker.getPosition());
        });

        $('#cur_loc' + options.id).click(function () {
            loader('cur_loc');
            gl.getCurrentPosition(displayPosition, displayError);
        });

        $('#toadr_loc' + options.id).click(function () {
            loader('toadr_loc');
            if ($.isFunction(autocomplete.getPlace) && typeof autocomplete.getPlace() != 'undefined' && autocomplete.getPlace().address_components) {
                var place = autocomplete.getPlace();

                setAddress(place.address_components);
                if ($('#f' + options.id + '_contacts_tel').length == 1 && place.formatted_phone_number) {
                    $('#f' + options.id + '_contacts_tel').val(place.formatted_phone_number);
                }
                if ($('#f' + options.id + '_contacts_tel').length == 1 && place.international_phone_number) {
                    $('#f' + options.id + '_contacts_tel').val(place.international_phone_number);
                }
                if ($('#f' + options.id + '_links_web').length == 1 && place.website) {
                    $('#f' + options.id + '_links_web').val(place.website);
                }
                if ($('#f' + options.id + '_address_company').length == 1 && place.name) {
                    var cn = true;

                    if(place.types && place.types.length > 0) {
                        $.each(place.types, function(k, v){
                            if($.inArray(v, ['political', 'administrative_area_level_2'])){
                                cn = false;
                            }
                        })
                    }

                    if(!cn) {
                        return;
                    }
                    $('#f' + options.id + '_address_company').val(place.name);
                }
            } else {
                setAddressFromMap();
            }
        });

        function setAddressFromMap() {
                var url = '//maps.googleapis.com/maps/api/geocode/json?language=' + options.lang + '&latlng=' + marker.getPosition().lat() + ',' + marker.getPosition().lng() + '&sensor=true'
                $.getJSON(url, function (data) {
                    reset();

                    if (data.status != 'OK') {
                        Joomcck.fieldError(options.id, options.strings.addrnotfound);
                        return;
                    }

                    setAddress(data.results[0].address_components);
                });
        }

        function setAddress(result) {

            if (!result) {
                return;
            }

            var address = [], city = [], state = [];

            $('input[id^="f' + options.id + '_address"]').val('');
            reset();

            $.each(result, function (k, v) {
                $.each(v.types, function (tk, tv) {
                    switch (tv) {
                        //case 'administrative_area_level_2':
                        case 'locality':
                        case 'postal_town':
                            //case 'political':
                            if ($.inArray(v.long_name, city) == -1) {
                                city.unshift(v.long_name);
                            }
                            break;

                        case 'sublocality':
                        case 'administrative_area_level_1':
                        case 'administrative_area_level_2':
                            if ($.inArray(v.long_name, state) == -1) {
                                state.push(v.long_name);
                            }
                            break;

                        case 'administrative_area_level_3':
                        case 'neighborhood':
                        case 'route':
                        case 'street_number':
                        case 'street_address':
                            if ($.inArray(v.long_name, address) == -1) {
                                address.push(v.long_name);
                            }
                            break;

                        case 'postal_code_prefix':
                        case 'postal_code':
                            $('#f' + options.id + '_address_zip').val(v.short_name);
                            break;
                        case 'country':
                            $('#f' + options.id + '_address_country').val(v.short_name);
                            break;
                    }
                });
            });

            if (city.join('')) $('#f' + options.id + '_address_city').val(city.join(', '));
            if (state.join('')) $('#f' + options.id + '_address_state').val(state.join(', '));
            if (address.join('')) $('#f' + options.id + '_address_address1').val(address.join(', '));
        }

        $('#adr_loc' + options.id).click(function () {
            loader('adr_loc');

            var address = [];
            if ($('#f' + options.id + '_address_address1').val()) {
                address.push($('#f' + options.id + '_address_address1').val());
            }
            if ($('#f' + options.id + '_address_address2').val()) {
                address.push($('#f' + options.id + '_address_address2').val());
            }
            if ($('#f' + options.id + '_address_city').val()) {
                address.push($('#f' + options.id + '_address_city').val());
            }
            if ($('#f' + options.id + '_address_state').val()) {
                address.push($('#f' + options.id + '_address_state').val());
            }
            if ($('#f' + options.id + '_address_zip').val()) {
                address.push($('#f' + options.id + '_address_zip').val());
            }
            if ($('#f' + options.id + '_address_country').val()) {
                address.push($('#f' + options.id + '_address_country').children('option:selected').text());
            }

            if (!address.join('')) {
                alert(options.strings.addrnotentered);
                reset();
                return;
            }

            geocoder.geocode({ 'address': address.join(', ')}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    marker.setPosition(results[0].geometry.location);
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(15);
                    setHiddenPosition(results[0].geometry.location);
                } else {
                    alert(options.strings.geocodefail + ' ' + status);
                }
                reset();
            });
        });

        var tab = $('#map_canvas_' + options.id).closest('.tab-pane').attr('id');
        if (tab) {
            $('a[href="#' + tab + '"]').one('click', function () {
                setTimeout(function () {
                    google.maps.event.trigger(map, 'resize');
                    initPosition();
                    map.setCenter(marker.getPosition());
                }, 100);
            });
        }

        function adrtypeahead(el, what) {

            if (!$(el).length) return;

            var labels = [], mapped = {}, post = {};

            if (!$.isFunction($(el).typeahead)) {
                return;
            }

            $(el).typeahead({
                items: options.limit || 10,
                source: function (query, process) {
                    post.q = query;
                    post.field_id = options.id;
                    post.func = what;
                    post.limit = 10;
                    post.country = $('#f' + options.id + '_address_country').val();
                    post.state = $('#f' + options.id + '_address_state').val();
                    post.city = $('#f' + options.id + '_address_city').val();
                    post.zip = $('#f' + options.id + '_address_zip').val();

                    return $.get(options.url || Joomcck.field_call_url, post, function (data) {
                        if (!data) return;
                        if (!data.result) return;

                        labels = [];
                        mapped = {};

                        $.each(data.result, function (i, item) {
                            mapped[item.label] = item.value
                            labels.push(item.label)
                        });

                        return process(labels);

                    }, 'json');
                },
                updater: function (item) {
                    return mapped[item];
                },
                highlighter: function (item) {
                    return item;
                }
            });
        }

        adrtypeahead('#f' + options.id + '_address_state', 'onTypeaheadState');
        adrtypeahead('#f' + options.id + '_address_city', 'onTypeaheadCity');
        adrtypeahead('#f' + options.id + '_address_zip', 'onTypeaheadZip');
    };


    $.fn.loadmap = function (params) {
        return new Geo(params, this);
    }
}(jQuery));