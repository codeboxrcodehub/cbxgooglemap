(function ($) {
    'use strict';

    function cbxgooglemap_render(element, $) {
        var $element = element;

        $element.empty();

        var $lat      = parseFloat($element.data('lat'));
        var $lng      = parseFloat($element.data('lng'));

        var $zoom     = $element.data('zoom');
        var $heading  = $element.data('heading');
        var $address  = $element.data('address');
        var $website  = $element.data('website');
        var $icon_url = $element.data('mapicon');

        var $map_type     = $element.data('maptype');
        var $map_source   = Number($element.data('mapsource'));
        var $scroll_wheel = Number($element.data('scrollwheel'));
        var $show_info    = Number($element.data('showinfo'));
        var $info_open    = Number($element.data('infow_open'));


        $show_info    = ($show_info === 1) ? true : false;
        $info_open    = ($info_open === 1) ? true : false;
        $scroll_wheel = ($scroll_wheel === 1) ? true : false;

        var $map, $map_icon;


        if ($map_source) {
            //if($lat > 0 && $lng > 0){
                // if ($apikey !== '') {
                // The location of Primary marker
                var $latlng = {lat: $lat, lng: $lng};

                //create a map using the primary location
                $map = new google.maps.Map($element[0], {
                    zoom: $zoom,
                    center: $latlng,
                    mapTypeId: $map_type,
                    disableDefaultUI: true,
                    scrollwheel: $scroll_wheel
                });


                if ($icon_url == '') {

                    $marker = new google.maps.Marker({
                        position: $latlng,
                        map: $map,
                        title: $heading,
                        draggable: false,
                        //icon: $map_icon
                    });
                } else {
                    $map_icon = {
                        url: $icon_url,
                        scaledSize: new google.maps.Size(50, 50)
                    };

                    $marker = new google.maps.Marker({
                        position: $latlng,
                        map: $map,
                        title: $heading,
                        draggable: false,
                        icon: $map_icon
                    });
                }


                // info popup
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

                    var $infowindow = new google.maps.InfoWindow({
                        content: $info_content
                    });

                    $marker.addListener('click', function () {
                        $infowindow.open({
                            anchor: $marker, $map
                        });
                    });

                    if ($info_open) {
                        $infowindow.open({
                            anchor: $marker, $map
                        });
                    }
                }
            //}
        }
        else {
            //if($lat > 0 && $lng > 0){
                //open street map
                $map = L.map($element.get(0)).setView([$lat, $lng], $zoom);
                //$map.invalidateSize();

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo($map);


                var $marker;

                if ($icon_url !== '') {
                    var $map_icon = L.icon({
                        iconUrl: $icon_url,
                        iconSize: [50, 50],
                        //popupAnchor: [15, 0],
                        iconAnchor: [16, 32], // bottom center of icon
                        popupAnchor: [10, -32] // shift popup arrow relative to icon
                    });

                    $marker = L.marker([$lat, $lng], {icon: $map_icon}).addTo($map);
                } else {
                    $marker = L.marker([$lat, $lng]).addTo($map);
                }


                if ($show_info === true) {
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
                }//end show info

                if ($scroll_wheel === false) {
                    $map.scrollWheelZoom.disable();
                }

                //add extra markers

                //title layer providers https://github.com/leaflet-extras/leaflet-providers/blob/master/leaflet-providers.js


                // CBXGOOGLEMAPEvents_do_action( 'cbxgooglemap_render', $element, $map, $map_source );

                // if ( typeof extraMarkers == 'function' ) {
                // 	extraMarkers(markers, $map);
                // }
            //}
        }

        CBXGOOGLEMAPEvents_do_action('cbxgooglemap_render_public', $element, $map, $map_source);

    }//end function cbxgooglemap_render

    $(document).ready(function ($) {
        $('.cbxgooglemap_embed').each(function (index, element) {
            var $element = $(element);
            var $render  = Number($element.data('render'));
            if (!$render) {
                $element.data('render', 1);
                cbxgooglemap_render($element, $);
            }
        });

    });//end dom ready

    //for elementor widget render
    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/cbxgooglemap_google_map.default', function ($scope, $) {
            var $element = $scope.find('.cbxgooglemap_embed');
            if (Number($element.length) > 0) {
                var $render = Number($element.data('render'));
                if (!$render) {
                    $element.data('render', 1);
                    cbxgooglemap_render($element, $);
                }
            }

        });
    });//end for elementor widget render

})(jQuery);
