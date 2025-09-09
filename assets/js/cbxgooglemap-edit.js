(function ($) {
    'use strict';

    function cbxgogolemap_copyStringToClipboard(str) {
        // Create new element
        var el   = document.createElement('textarea');
        // Set value (string to be copied)
        el.value = str;
        // Set non-editable to avoid focus and move outside of view
        el.setAttribute('readonly', '');
        el.style = {position: 'absolute', left: '-9999px'};
        document.body.appendChild(el);
        // Select text inside element
        el.select();
        // Copy text to clipboard
        document.execCommand('copy');
        // Remove temporary element
        document.body.removeChild(el);
    }//end function cbxgogolemap_copyStringToClipboard

    $(document).ready(function ($) {
        var cbxgooglemap_awn_options = {
            labels: {
                tip          : cbxgooglemap_edit.awn_options.tip,
                info         : cbxgooglemap_edit.awn_options.info,
                success      : cbxgooglemap_edit.awn_options.success,
                warning      : cbxgooglemap_edit.awn_options.warning,
                alert        : cbxgooglemap_edit.awn_options.alert,
                async        : cbxgooglemap_edit.awn_options.async,
                confirm      : cbxgooglemap_edit.awn_options.confirm,
                confirmOk    : cbxgooglemap_edit.awn_options.confirmOk,
                confirmCancel: cbxgooglemap_edit.awn_options.confirmCancel
            }
        };

        let notifier = new AWN(cbxgooglemap_awn_options);

        var $cbxmap = null;

        //Initiate Color Picker
        //$('.cbxgooglemapmeta_colorpicker').wpColorPicker();

        $('.selecttwo-select').select2({
            placeholder: cbxgooglemap_edit.please_select,
            allowClear : false
        });


        $(document).on('click', '.cbxgooglemapmeta_filepicker_btn', function (event) {
            event.preventDefault();

            var self = $(this);

            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media({
                title   : self.data('uploader_title'),
                button  : {
                    text: self.data('uploader_button_text')
                },
                multiple: false
            });

            file_frame.on('select', function () {
                var attachment = file_frame.state().get('selection').first().toJSON();

                var picker_wrapper = self.closest('.cbxgooglemapmeta_input_file_wrap');

                picker_wrapper.find('.cbxgooglemapmeta_filepicker').val(attachment.url);
                picker_wrapper.find('.cbxgooglemapmeta_marker').css({
                    'background-image': 'url("' + attachment.url + '")'
                }).removeClass('cbxgooglemapmeta_marker_hide');
                picker_wrapper.find('.cbxgooglemapmeta_trash').removeClass('cbxgooglemapmeta_trash_hide');
                picker_wrapper.find('.cbxgooglemapmeta_filepicker_btn').addClass('cbxgooglemapmeta_filepicked').removeClass('cbxgooglemapmeta_left_space');
            });

            // Finally, open the modal
            file_frame.open();
        });

        // for icon delete functionality
        $(document).on('click', '.cbxgooglemapmeta_input_file_wrap .cbxgooglemapmeta_trash', function () {
            var picker_wrapper = $(this).closest('.cbxgooglemapmeta_input_file_wrap');
            picker_wrapper.find('.cbxgooglemapmeta_input_file').val('');
            picker_wrapper.find('.cbxgooglemapmeta_marker').addClass('cbxgooglemapmeta_marker_hide');
            picker_wrapper.find('.cbxgooglemapmeta_filepicker_btn').removeClass('cbxgooglemapmeta_filepicked').addClass('cbxgooglemapmeta_left_space');
            $(this).addClass('cbxgooglemapmeta_trash_hide');
        });

        // Switches option sections
        $('.metabox-content-cbxgooglemap').hide();
        var activetab = '';
        if (typeof (localStorage) !== 'undefined') {
            activetab = localStorage.getItem('cbxgooglemapmetaactivetab');

        }
        if (activetab !== '' && $(activetab).length) {
            $(activetab).fadeIn();
        } else {
            $('.metabox-content-cbxgooglemap:first').fadeIn();
        }

        $('.metabox-content-cbxgooglemap .collapsed').each(function () {
            $(this).find('input:checked').parent().parent().parent().nextAll().each(
                function () {
                    if ($(this).hasClass('last')) {
                        $(this).removeClass('hidden');
                        return false;
                    }
                    $(this).filter('.hidden').removeClass('hidden');
                });
        });


        if (activetab !== '' && $(activetab + '-tab').length) {
            $(activetab + '-tab').addClass('nav-tab-active');

            if (activetab + '-tab' === '#metabox-contentmaplocation-tab') {
                cbxgooglemapmeta_render();
            }
        } else {
            $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
        }

        $('.nav-tab-wrapper a').on('click', function (evt) {
            evt.preventDefault();

            $('.nav-tab-wrapper a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active').blur();
            var clicked_group = $(this).attr('href');
            if (typeof (localStorage) !== 'undefined') {
                localStorage.setItem('cbxgooglemapmetaactivetab', $(this).attr('href'));
            }
            $('.metabox-content-cbxgooglemap').hide();
            $(clicked_group).fadeIn();


            if ($(this).attr('id') == 'metabox-contentmaplocation-tab' && typeof $cbxmap !== null) {
                cbxgooglemapmeta_render();
            }
        });

        var cbxmap = '';


        function cbxgooglemapmeta_render() {
            //backend map integration
            $('.cbxgooglemapmeta_input_location').each(function (index, element) {
                var $element = $(element);

                var $parent = $element.closest('.metabox-holder-cbxgooglemap');


                var $zoom         = $parent.find('.cbxgooglemapmeta_input_zoom');
                var $zoom_val     = Number($zoom.val());
                var $show_info    = Number($("input[name='_cbxgooglemapshowinfo']:checked").val());
                var $info_open    = Number($("input[name='_cbxgooglemapinfow_open']:checked").val());
                var $scroll_wheel = Number($("input[name='_cbxgooglemapscrollwheel']:checked").val());


                var $heading = $parent.find('.cbxgooglemapmeta_input_title').val();
                var $address = $parent.find('.cbxgooglemapmeta_input_address').val();
                var $website = $parent.find('.cbxgooglemapmeta_input_website').val();


                var $lat         = $('#metabox-contentmaplocation .cbxgooglemapmeta_input_lat');
                var $lng         = $('#metabox-contentmaplocation .cbxgooglemapmeta_input_lng');
                var $current_lat = Number($lat.val());
                var $current_lng = Number($lng.val());
                var $icon_url    = $('#metabox-contentmaplocation').find('.cbxgooglemapmeta_filepicker').val();


                if (!$icon_url) {
                    $icon_url = cbxgooglemap_edit.icon_url_default;
                }

                var $meta_map   = $parent.find('.map_canvas');
                var $map_source = Number($meta_map.data('mapsource'));
                var $map_type   = $("select[name='_cbxgooglemapmaptype']").val();
                var $api_key    = $meta_map.data('apikey');

                if ($map_source === 1) {
                    //google map
                    if ($api_key !== '') {
                        // The location of Primary marker
                        var $latlng = {lat: $current_lat, lng: $current_lng};

                        // The map, centered at Primary marker
                        $cbxmap = new google.maps.Map($meta_map[0], {
                            zoom            : $zoom_val,
                            center          : $latlng,
                            mapTypeId       : $map_type,
                            disableDefaultUI: true
                        });

                        // Make sure you load Google Maps with the Places library:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>

// Attach autocomplete to your input element
                        var input = $element[0]; // jQuery to raw DOM element
                        var autocomplete = new google.maps.places.Autocomplete(input);

// Optional: bias search results to the map’s current viewport
                        autocomplete.bindTo("bounds", $cbxmap);

// Handle when a user selects a place from the dropdown
                        autocomplete.addListener("place_changed", function () {
                            var place = autocomplete.getPlace();

                            if (!place.geometry || !place.geometry.location) {
                                //alert("No details available for input: '" + place.name + "'");
                                notifier.alert(cbxgooglemap_edit.no_address_found);
                                return;
                            }

                            // If the place has a viewport, fit map to it. Otherwise, center directly.
                            if (place.geometry.viewport) {
                                $cbxmap.fitBounds(place.geometry.viewport);
                            } else {
                                $cbxmap.setCenter(place.geometry.location);
                                $cbxmap.setZoom(15); // default zoom if no viewport
                            }

                            // Update marker position
                            $marker.setPosition(place.geometry.location);

                            // Update hidden fields
                            $lat.val(place.geometry.location.lat());
                            $lng.val(place.geometry.location.lng());

                            // Optional: update zoom field if you have one
                            $zoom.val($cbxmap.getZoom());

                            // Debug: see what you got
                            //console.log("Selected place:", place);
                            //console.log("Formatted address:", place.formatted_address);
                            //console.log("Components:", place.address_components);


                            // ===== Address breakdown =====
                            var address1 = "";
                            var address2 = "";

                            if (place.address_components) {
                                var street_number = "";
                                var route = "";
                                var city = "";
                                var state = "";
                                var postal = "";
                                var country = "";

                                place.address_components.forEach(function (comp) {
                                    if (comp.types.includes("street_number")) {
                                        street_number = comp.long_name;
                                    }
                                    if (comp.types.includes("route")) {
                                        route = comp.long_name;
                                    }
                                    if (comp.types.includes("locality")) {
                                        city = comp.long_name;
                                    }
                                    if (comp.types.includes("administrative_area_level_1")) {
                                        state = comp.short_name;
                                    }
                                    if (comp.types.includes("postal_code")) {
                                        postal = comp.long_name;
                                    }
                                    if (comp.types.includes("country")) {
                                        country = comp.long_name;
                                    }
                                });

                                // Line 1: street number + route
                                address1 = (street_number + " " + route).trim();

                                // Line 2: city, state postal, country
                                address2 = [city, state, postal, country].filter(Boolean).join(", ");
                            }

                            // Put into your field (format: line1, line2)
                            $parent.find(".cbxgooglemapmeta_input_address").val(address1 + (address2 ? ", " + address2 : ""));
                        });



                        if ($icon_url == '') {
                            /*var $map_icon = {
                                url: $icon_url,
                                scaledSize: new google.maps.Size(50, 50)
                            };*/

                            $marker = new google.maps.Marker({
                                position : $latlng,
                                map      : $cbxmap,
                                title    : $heading,
                                draggable: true,
                                //icon: $map_icon,
                            });
                        } else {
                            var $map_icon = {
                                url       : $icon_url,
                                scaledSize: new google.maps.Size(50, 50)
                            };

                            $marker = new google.maps.Marker({
                                position : $latlng,
                                map      : $cbxmap,
                                title    : $heading,
                                draggable: true,
                                icon     : $map_icon,
                            });
                        }


                        if ($show_info) {
                            var $info_content = '';
                            var $heading_html = '';
                            var $address_html = '';

                            if ($heading !== '') {
                                if ($website) {
                                    $heading_html = '<h3 class="jqcbxgoglemap_info_heading"><a href="' + $website + '" target="_blank">' + $heading + '</a></h3>';
                                } else {
                                    $heading_html = '<h3 class="jqcbxgoglemap_info_heading">' + $heading + '</h3>';
                                }
                            }

                            if ($address !== '') {
                                $address_html = '<div class="jqcbxgoglemap_info_body">' + $address + '</div>';
                            }


                            if ($heading_html !== '' || $address_html !== '') {
                                $info_content = '<div class="jqcbxgoglemap_info">' + $heading_html + ' ' + $address_html + '</div>';
                            }

                            // info popup
                            var infowindow = new google.maps.InfoWindow({
                                content: $info_content
                            });

                            $marker.addListener('click', function () {
                                infowindow.open({
                                    anchor: $marker,
                                    $cbxmap
                                });
                            });

                            if ($info_open) {
                                infowindow.open({
                                    anchor: $marker,
                                    $cbxmap
                                });
                            }
                        }

                        $marker.addListener('dragend', function (e) {
                            $lat.val(e.latLng.lat());
                            $lng.val(e.latLng.lng());
                        });


                    }//end if apy key exits

                }
                else {
                    //open street map

                    //at first destroy the map
                    if ($cbxmap && $cbxmap.remove) {
                        $cbxmap.off();
                        $cbxmap.remove();
                    }

                    $cbxmap = L.map($meta_map[0]).setView([$current_lat, $current_lng], $zoom_val);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo($cbxmap);


                    // Add OSM geocoder control
                    //json data https://gist.github.com/manchumahara/c4a81781c3bec1588b175ae762f8f8e5
                    var osmGeocoder = L.Control.geocoder({
                        geocoder          : new L.Control.Geocoder.Nominatim(),
                        placeholder       : cbxgooglemap_edit.search_address,
                        defaultMarkGeocode: false,
                        collapsed         : false,
                        position          : 'topright'
                    }).on('markgeocode', function (e) {
                        var bbox    = e.geocode.bbox;
                        var center  = bbox.getCenter();
                        var geocode = e.geocode;

                        // Update map view and marker position
                        $cbxmap.setView(center, $cbxmap.getZoom());
                        $marker.setLatLng(center);
                        $lat.val(center.lat);
                        $lng.val(center.lng);

                        // Get additional information from the geocode result
                        if (geocode.properties && geocode.properties.address) {
                            var address = geocode.properties.address;

                            // Build address line 1
                            var addressLine1 = '';
                            if (address.house_number) {
                                addressLine1 += address.house_number + ' ';
                            }
                            if (address.road) {
                                addressLine1 += address.road;
                            }

                            // Build address line 2
                            var addressLine2 = '';
                            if (address.suburb) {
                                addressLine2 += address.suburb;
                            } else if (address.neighbourhood) {
                                addressLine2 += address.neighbourhood;
                            } else if (address.district) {
                                addressLine2 += address.district;
                            }

                            // Combine address lines
                            var fullAddress = addressLine1;
                            if (addressLine1 && addressLine2) {
                                fullAddress += ', ' + addressLine2;
                            } else if (addressLine2) {
                                fullAddress = addressLine2;
                            }

                            // Update the address field
                            if (fullAddress) {
                                $parent.find('.cbxgooglemapmeta_input_address').val(fullAddress);
                            }

                            // Update other address components if corresponding fields exist
                            /*if (address.city || address.town || address.village) {
                                var city      = address.city || address.town || address.village;
                                var cityField = $parent.find('.cbxgooglemapmeta_input_city');
                                if (cityField.length) {
                                    cityField.val(city);
                                }
                            }

                            if (address.state || address.county) {
                                var state      = address.state || address.county;
                                var stateField = $parent.find('.cbxgooglemapmeta_input_state');
                                if (stateField.length) {
                                    stateField.val(state);
                                }
                            }

                            if (address.country) {
                                var countryField = $parent.find('.cbxgooglemapmeta_input_country');
                                if (countryField.length) {
                                    countryField.val(address.country);
                                }
                            }

                            if (address.postcode) {
                                var postcodeField = $parent.find('.cbxgooglemapmeta_input_postcode');
                                if (postcodeField.length) {
                                    postcodeField.val(address.postcode);
                                }
                            }*/
                        }

                        // Update heading if display_name is available and no heading is set
                        //if (geocode.name && !$parent.find('.cbxgooglemapmeta_input_location').val()) {
                        if (geocode.name) {
                            //$parent.find('.cbxgooglemapmeta_input_title').val(geocode.name);
                            $parent.find('.cbxgooglemapmeta_input_location').val(geocode.name);
                        }

                        // Fit map to bounding box if it's reasonable
                        if (bbox.getNorthEast().distanceTo(bbox.getSouthWest()) < 10000) { // 10km
                            $cbxmap.fitBounds(bbox);
                            $zoom.val($cbxmap.getZoom());
                        }
                    }).addTo($cbxmap);

                    // Enable search as you type
                    var geocoderInput = $('.leaflet-control-geocoder-form input');
                    var searchTimeout;

                    geocoderInput.on('input', function () {
                        var query = $(this).val();

                        // Clear previous timeout
                        clearTimeout(searchTimeout);

                        // Only search if we have at least 3 characters
                        if (query.length >= 3) {
                            // Set a new timeout to avoid too many requests while typing
                            searchTimeout = setTimeout(function () {
                                // Trigger the geocoder search
                                osmGeocoder._geocode(query);
                            }, 300); // 300ms delay
                        }
                    });

                    // Override the geocoder's _markGeocode method to ensure our event handler is called
                    var originalMarkGeocode  = osmGeocoder._markGeocode;
                    osmGeocoder._markGeocode = function (result) {
                        // Call the original method
                        originalMarkGeocode.call(this, result);

                        // Trigger our custom event
                        this.fire('markgeocode', {
                            geocode: result
                        });
                    };

                    // Enable search as you type
                    var geocoderInput = $('.leaflet-control-geocoder-form input');
                    var searchTimeout;

                    geocoderInput.on('input', function () {
                        var query = $(this).val();

                        // Clear previous timeout
                        clearTimeout(searchTimeout);

                        // Only search if we have at least 3 characters
                        if (query.length >= 3) {
                            // Set a new timeout to avoid too many requests while typing
                            searchTimeout = setTimeout(function () {
                                // Trigger the geocoder search
                                osmGeocoder._geocode(query);
                            }, 300); // 300ms delay
                        }
                    });

                    // Override the geocoder's _markGeocode method to ensure our event handler is called
                    var originalMarkGeocode  = osmGeocoder._markGeocode;
                    osmGeocoder._markGeocode = function (result) {
                        // Call the original method
                        originalMarkGeocode.call(this, result);

                        // Trigger our custom event
                        this.fire('markgeocode', {
                            geocode: result
                        });
                    };


                    var $marker;

                    if ($icon_url !== '') {
                        var $map_icon = L.icon({
                            iconUrl    : $icon_url,
                            iconSize   : [50, 50],
                            popupAnchor: [15, 0]
                        });

                        $marker = L.marker([$current_lat, $current_lng], {
                            draggable: true,
                            icon     : $map_icon
                        }).addTo($cbxmap);
                    } else {
                        $marker = L.marker([$current_lat, $current_lng], {
                            draggable: true
                        }).addTo($cbxmap);
                    }


                    if ($show_info === 1) {
                        var $heading_html = '';
                        var $address_html = '';

                        if ($heading !== '') {
                            if ($website) {
                                $heading_html = '<h3 class="jqcbxgoglemap_info_heading"><a href="' + $website + '" target="_blank">' + $heading + '</a></h3>';
                            } else {
                                $heading_html = '<h3 class="jqcbxgoglemap_info_heading">' + $heading + '</h3>';
                            }
                        }


                        if ($address !== '') {
                            $address_html = '<div class="jqcbxgoglemap_info_body">' + $address + '</div>';
                        }

                        if ($heading_html !== '' || $address_html !== '') {
                            if ($info_open) {
                                $marker.bindPopup('<div class="jqcbxgoglemap_info">' + $heading_html + '' + $address_html + '</div>').openPopup();
                            } else {
                                $marker.bindPopup('<div class="jqcbxgoglemap_info">' + $heading_html + '' + $address_html + '</div>').on('click', function (event) {
                                    //event.target.openPopup;
                                });
                            }
                        }
                    }

                    if ($scroll_wheel === 1) {
                        $cbxmap.scrollWheelZoom.disable();
                    }

                    $marker.on('dragend', function (e) {
                        $lat.val($marker.getLatLng().lat);
                        $lng.val($marker.getLatLng().lng);
                    });

                    $cbxmap.on('zoomend', function (e) {
                        $zoom.val($cbxmap.getZoom());
                    });


                }//end if openstreetmap

                CBXGOOGLEMAPEvents_do_action('cbxgooglemap_render_meta', $parent.find('.map_canvas'), $cbxmap, $map_source);


            });//end google map
        }


        //select shortcode text and copy to clipboard
        /*$(document).on('click', '.cbxgooglemapshortcodecopytrigger', function (e) {

            var text = $(this).data('clipboard-text');
            var successText = $(this).data('success');
            var $input = $('<input class="cbxgooglemapshortcode-text" type="text">');
            $input.prop('value', text);
            $input.insertAfter('body');
            $input.select();

            try {
                document.execCommand('copy');
                $('.cbxgooglemapshortcode-text').remove();
                $(this).after('<span id="copied-text">' + successText + '</span>');
                $('#copied-text').fadeOut(1000, function () {
                    $(this).remove();
                });
            } catch (err) {

            }
        });*/


        //click to copy shortcode
        $('.cbxballon_ctp').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            cbxgogolemap_copyStringToClipboard($this.prev('.cbxshortcode').text());

            $this.attr('aria-label', cbxgooglemap_edit.copycmds.copied_tip);

            window.setTimeout(function () {
                $this.attr('aria-label', cbxgooglemap_edit.copycmds.copy_tip);
            }, 1000);
        });

        $('.wrap').addClass('cbx-chota cbxgooglemap-page-wrapper cbxgooglemap-addedit-wrapper');
        $('#search-submit').addClass('button primary');
        $('#post-query-submit').addClass('button primary');
        $('.button.action').addClass('button primary');
        $('.save-post-status').addClass('button primary mt-10');
        $('.save-post-visibility').addClass('button primary mt-10');
        $('.save-timestamp').addClass('button primary mt-10');
        $('.preview.button').addClass('button secondary');
        $('.button.tagadd').addClass('button secondary');
        $('.cancel-post-status').addClass('button secondary mt-10');
        $('.cancel-post-visibility').addClass('button secondary mt-10');
        $('.cancel-timestamp').addClass('button secondary mt-10');
        $('.page-title-action').addClass('button primary');
        $('#save-post').addClass('button primary');
        $('#publish').addClass('button primary');
        $('#screen-meta').addClass('cbx-chota cbxgooglemap-page-wrapper cbxgooglemap-logs-wrapper');
        $('#screen-options-apply').addClass('primary');
        $('#title').attr('placeholder', cbxgooglemap_edit.map_title_placeholder);

    });
})(jQuery);