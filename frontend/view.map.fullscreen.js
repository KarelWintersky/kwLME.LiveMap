var current_infobox_region_id = '';
var map;
var polymap;

$(function(){
    // умолчательные действия
    $(".leaflet-container").css('background-color', theMap['display']['background_color']);

    polymap = buildPolymap(theMap);

    map = L.map('map', {
        crs: L.CRS.Simple,
        minZoom: -3,
        maxZoom: 2,
        preferCanvas: true,
        renderer: L.canvas(),
        zoomControl: false,
    });

    map.addControl(new L.Control.Zoomslider({position: 'bottomright'}));

    var current_bounds  = [ [0, 0], [theMap['map']['height'], theMap['map']['width'] ] ];

    var image = L.imageOverlay( theMap['map']['imagefile'], current_bounds).addTo(map);

    if (theMap['maxbounds']) {
        var mb = theMap['maxbounds'];
        map.setMaxBounds([ [ mb['topleft_h'] * theMap['map']['height'], mb['topleft_w'] * theMap['map']['width'] ]  , [ mb['bottomright_h'] * theMap['map']['height'], mb['bottomright_w'] * theMap['map']['width'] ] ]);
    }

    map.setZoom( theMap['display']['zoom'] );

    var poly_layer = new L.LayerGroup();

    // draw polygons on map, bind on-click function
    Object.keys( polymap ).forEach(function( id_region ) {
        poly_layer.addLayer(
            polymap[ id_region ].on('click', function(){

                window.location.hash = 'view=[' + id_region + ']';
                var t = (theMap['regions'][ id_region ]['title'] != '')
                    ? theMap['regions'][ id_region ]['title']
                    : '';

                toggleContentViewBox(id_region, t);
            })
        );
    });

    poly_layer.addTo(map);

    createControl_RegionsBox();
    createControl_InfoBox();
    createControl_Backward();

    // не показываем контрол "назад" если страница загружена в iframe
    if (! (window != window.top || document != top.document || self.location != top.location)) {
        map.addControl( new L.Control.Backward() );
    }

    // показываем контентный регион только если есть список регионов с данными
    if (regions_with_content_ids.length) {
        map.addControl( new L.Control.RegionsBox() );
    }

    // его надо создавать только когда заявили показ информации!
    var __InfoBox = new L.Control.InfoBox();
    map.addControl( __InfoBox );

    if (true) {
        var wlh_options = wlhBased_GetAction(polymap);

        map.fitBounds(current_bounds);

        if (wlh_options) {
            do_RegionShowInfo(wlh_options);
            do_RegionFocus(wlh_options, polymap);
        } else {
            map.fitBounds(current_bounds);
        }
    }

    // zoom control (а если сектора нет?)
    map.on('zoomend', function() {
        var currentZoom = map.getZoom();
        console.log("Current zoom: " + currentZoom);
        /*if (sector == null) return;

         if (currentZoom < sector_options.zoom_threshold) {
         group.clearLayers();
         // map.removeLayer(sector);
         } else {
         group.addLayer( sector );
         // map.addLayer(sector);
         }*/
    });


}).on('click', '#actor-regions-toggle', function (el) {
        toggleRegionsBox(this);
    })
    .on('click', '#actor-viewbox-toggle', function (el) {
        toggleInfoBox(this);
    })
    .on('click', "#actor-backward-toggle", function (el) {
        var state = $(this).data('content-is-visible');
        var text = (state == false) ? '&lt;' : '&gt;'; //@todo: сообщения на активном/свернутом виде перенести в дата-атрибуты
        $(this).html(text);

        var data = $(this).data('content');
        $('#' + data).toggle();
        $(this).data('content-is-visible', !state);
    })
    .on('change', "#sort-select", function(e){
        var must_display = (e.target.value == 'total') ? "#data-ordered-alphabet" : "#data-ordered-latest";
        var must_hide = (e.target.value == 'total') ? "#data-ordered-latest" : "#data-ordered-alphabet";
        $(must_hide).hide();
        $(must_display).show();
    })
    .on('change', "#sort-select", function(e){
        var must_display = (e.target.value == 'total') ? "#data-ordered-alphabet" : "#data-ordered-latest";
        var must_hide = (e.target.value == 'total') ? "#data-ordered-latest" : "#data-ordered-alphabet";
        $(must_hide).hide();
        $(must_display).show();
    })
    .on('click', '.action-focus-at-region', function(){
        // клик на ссылке в списке регионов
        do_RegionFocus({
            action: 'focus',
            region_id: $(this).data('region-id')
        }, polymap);
    })
    .on('click', '#actor-edit', function(){
        var region_id = $(this).data('region-id');
        document.location.href = '/edit/region?map=' + map_alias + '&id=' + region_id;
    });