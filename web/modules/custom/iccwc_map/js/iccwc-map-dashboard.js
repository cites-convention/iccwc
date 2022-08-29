(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.partyListMap = {
    attach: function attach(context) {
      $('.parties-map-overview').once('partiesMap').each(function () {

        // Find and color parties
        var matchExpression = ['match', ['get', 'ISO3CD']];
        console.log(matchExpression);
        var data = drupalSettings.parties;
        var coords = drupalSettings.coordinates;
        data.forEach(function (row) {
          var default_color = 'rgb(232,188,5)';
          var color = row['color'];
          if (color) {
            var rgb_color = hexToRgb(color);
            matchExpression.push(row['iso'], rgb_color);
          } else {
            matchExpression.push(row['iso'], default_color);
          }

        });
        matchExpression.push('rgb(165, 163, 157)');

        let map = new mapboxgl.Map({
          container: 'parties-map-overview',
          // attributionControl: false,
          hash: false,
          // renderWorldCopies: false,
          maxZoom: 3,
          zoom: 1.1,
          center: [0, 10],
          style: {
            version: 8,
            sources: {
              v: {
                type: 'vector',
                tiles: ['https://UN-Geospatial.github.io/cartotile-plain-design/data/cartotile_v01/{z}/{x}/{y}.pbf'],
                attribution: '<table><tr><td style="font-size: 7pt; line-height: 100%">The boundaries and names shown and the designations used on this map do not imply official endorsement or acceptance by the United Nations.​ Final boundary between the Republic of Sudan and the Republic of South Sudan has not yet been determined.​<br>* Non-Self Governing Territories<br>** Dotted line represents approximately the Line of Control in Jammu and Kashmir agreed upon by India and Pakistan. The final status of Jammu and Kashmir has not yet been agreed upon by the parties.​<br>*** A dispute exists between the Governments of Argentina and the United Kingdom of Great Britain and Northern Ireland concerning sovereignty over the Falkland Islands (Malvinas).</td><td  style="font-size: 5pt; color: #009EDB" valign="bottom">Powered by<br><img src="https://unopengis.github.io/watermark/watermark.png" alt="UN OpenGIS logo" width="50" height="50"></td></tr></table>',
                maxzoom: 3,
                minzoom: 0
              }
            },
            glyphs: 'https://UN-Geospatial.github.io/cartotile-plain-design/font/{fontstack}/{range}.pbf',
            transition: {
              duration: 0,
              delay: 0
            },
            layers: [
              {
                id: 'background',
                type: 'background',
                layout: {'visibility':'visible'},
                paint: {
                  'background-color': ['rgb', 255, 255, 255]
                }
              },
              {
                id: 'bnda',
                type: 'fill',
                source: 'v',
                'source-layer': 'bnda',
                maxzoom: 3,
                minzoom: 0,
                paint: {
                  'fill-color': matchExpression,
                  'fill-opacity': 0.8
                }
              },
              {
                id: 'bndl_solid',
                type: 'line',
                source: 'v',
                'source-layer': 'bndl',
                maxzoom: 3,
                minzoom: 0,
                filter: [
                  'any',
                  ['==', 'BDYTYP', 1],
                  ['==', 'BDYTYP', 0],
                  ['==', 'BDYTYP', 2]
                ],
                paint: {
                  'line-color': ['rgb', 255, 255, 255],
                  'line-width': 0.8
                }
              },
              {
                id: 'bndl_dashed',
                type: 'line',
                source: 'v',
                'source-layer': 'bndl',
                maxzoom: 3,
                minzoom: 0,
                filter: [
                  'all',
                  ['==', 'BDYTYP', 3]
                ],
                paint: {
                  'line-color': ['rgb', 255, 255, 255],
                  'line-dasharray': [3,2],
                  'line-width': 0.8
                }
              },
              {
                id: 'bndl_dotted',
                type: 'line',
                source: 'v',
                'source-layer': 'bndl',
                maxzoom: 3,
                minzoom: 0,
                filter: [
                  'all',
                  ['==', 'BDYTYP', 4]
                ],
                paint: {
                  'line-color': ['rgb', 255, 255, 255],
                  'line-dasharray': [1,2],
                  'line-width': 0.8
                }
              },
              {
                id: 'hide_ata',
                type: 'fill',
                source: 'v',
                'source-layer': 'bnda',
                maxzoom: 3,
                minzoom: 0,
                filter: ['==', 'ISO3CD', 'ATA'],
                paint: {
                  'fill-color': ['rgb', 255, 255, 255],
                  'fill-opacity': 1
                }
              },
            ]
          }
        });

        coords.forEach(function (row) {
          var default_color = 'rgb(232,188,5)';
          var color = row['color'];
          let marker;
          const coordinate = [row['long']-10, row['lat']];

          if (color) {
            var rgb_color = hexToRgb(color);
            marker = new mapboxgl.Marker({color: rgb_color})
              .setLngLat(coordinate)
              .setPopup(
                new mapboxgl.Popup({ closeButton: false, closeOnClick: true, offset: 25 })
                  .setHTML(pointInformation(row, ''))
              )
              .addTo(map);
          } else {
            marker = new mapboxgl.Marker({color: default_color})
              .setLngLat(coordinate)
              .setPopup(
                new mapboxgl.Popup({ closeButton: false, closeOnClick: true, offset: 25 })
                  .setHTML(pointInformation(row, ''))
              )
              .addTo(map);
          }
        });

        map.on('load', ()=> {
          map.addControl(new mapboxgl.NavigationControl())
          map.resize();
        });

        var popup = new mapboxgl.Popup({
          closeButton: false,
          closeOnClick: false
        });

        map.on('mousemove', 'bnda', function(e) {

          var is_party = data.filter(function(el) {
            return el.iso === e.features[0].properties.ISO3CD;
          });


          var layers = map.getStyle().layers;
          var current_country_layer = layers.filter(function(el) {
            return el.id === 'current_country'
          });

          if (is_party.length > 0) {
            if (typeof current_country_layer !== "undefined" && current_country_layer.length <= 0) {
              map.addLayer(
                {
                  id: 'current_country',
                  type: 'fill',
                  source: 'v',
                  'source-layer': 'bnda',
                  maxzoom: 4,
                  minzoom: 0,
                  filter: [
                    'all',
                    ['==', 'ISO3CD', e.features[0].properties.ISO3CD]
                  ],
                  paint: {
                    "fill-color": "#00757D",
                  }
                },
              );
            }
            map.getCanvas().style.cursor = 'pointer';
            // a temporary fix for issue #11129
            if (e.features[0].properties.ISO3CD === 'TWN' || e.features[0].properties.ISO3CD === "CHN") {
              map.setFilter('current_country', [
                "in",
                "ISO3CD",
                'CHN',
                'TWN'
              ])
            } else {
              map.setFilter('current_country', ['all', ['==', 'ISO3CD', e.features[0].properties.ISO3CD]]);
            }
          }

          if(e.features[0].properties.STSCOD === 1 && is_party.length > 0){
            var html = "<span class='stscod1'><b>" + e.features[0].properties.MAPLAB + "</b></span><br/>";
            html = partyInformation(e, data, html);

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map);
          } else if (e.features[0].properties.STSCOD === 2 && is_party.length > 0){
            var html = "<span class='stscod2'>" + e.features[0].properties.MAPLAB + "</span><br/>";
            html = partyInformation(e, data, html);

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map)
          } else if (e.features[0].properties.STSCOD === 3 && is_party.length > 0){

            if(e.features[0].properties.MAPLAB === "Falkland Islands (Malvinas) ***"){

              var html = "<span class='stscod3'>" + e.features[0].properties.MAPLAB + "</span><br/>";
              html = partyInformation(e, data, html);

            } else {
              var html = "<span class='stscod3'>" + e.features[0].properties.MAPLAB + "</span><br/>";
              html = partyInformation(e, data, html);

            }

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map)
          } else if (e.features[0].properties.STSCOD === 4 && is_party.length > 0){
            var html = "<span class='stscod4'>" + e.features[0].properties.MAPLAB + "</span><br/>";
            html = partyInformation(e, data, html);

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map)
          } else if (e.features[0].properties.STSCOD === 5 && is_party.length > 0){
            var html = "<span class='stscod5'>" + e.features[0].properties.MAPLAB + "</span><br/>";
            // a temporary fix for issue #11129
            if (e.features[0].properties.ISO3CD === 'TWN') {
              var html = "<span class='stscod1'><b>CHINA</b></span><br/>";
            }
            html = partyInformation(e, data, html);

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map)
          } else if (e.features[0].properties.STSCOD === 6 && is_party.length > 0){
            var html = "<span class='stscod6'>" + e.features[0].properties.MAPLAB + "</span><br/>";
            html = partyInformation(e, data, html);

            popup
              .setLngLat(e.lngLat)
              .setHTML(html)
              .addTo(map)
          } else if (e.features[0].properties.STSCOD === 99 && is_party.length > 0){

            if(e.features[0].properties.MAPLAB === "Jammu and Kashmir **"){
              var html = "<span class='stscod6'>" + e.features[0].properties.MAPLAB + "</span><br/>";
              html = partyInformation(e, data, html);

              popup
                .setLngLat(e.lngLat)
                .setHTML(html)
                .addTo(map)
            } else {
              popup.remove();
              if (map.getLayer("current_country")) {
                map.removeLayer("current_country");
              }
            }
          }
        });

        map.on('mouseleave', 'bnda', function(e){
          var layers = map.getStyle().layers;
          for(var i=0; i < layers.length; i++) {
            if(layers[i].id === 'current_country') {
              if (map.getLayer("current_country")) {
                map.removeLayer("current_country");
              }
            }
          }
          var current_country_layer = layers.filter(function(el) {
            return el.id === 'current_country'
          });
          if (current_country_layer.length > 0 && typeof current_country_layer !== "undefined") {
              if (map.getLayer("current_country")) {
                map.removeLayer("current_country");
              }
          }

          map.getCanvas().style.cursor = '';
          popup.remove();
        });

        // Fly to region
        // Global
        document.getElementById('global').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              0, 10
            ],
            essential: true,
            zoom: 1.1
          });
        });
        // Africa
        document.getElementById('region-africa').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              8.7832, 1.5085
            ],
            essential: true,
            zoom: 2
          });
        });

        // Asia
        document.getElementById('region-asia').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              100.9851, 10.5344
            ],
            essential: true,
            zoom: 2
          });
        });

        // Central and Eastern Europe
        document.getElementById('region-europe').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              -5.6569, 50.6569
            ],
            essential: true,
            zoom: 2.9
          });
        });

        // Central and South America and the Caribbean
        document.getElementById('region-latin_america').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              -78.6569, 20.6569
            ],
            essential: true,
            zoom: 2
          });
        });

        // North America
        document.getElementById('region-north_america').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              -99.7084, 35.3804
            ],
            essential: true,
            zoom: 2
          });
        });

        // Oceania
        document.getElementById('region-oceania').addEventListener('click', function () {
          removeClass();
          $(this).addClass('active');
          map.flyTo({
            center: [
              121.8771, -24.5965
            ],
            essential: true,
            zoom: 2
          });
        });

      });

      function removeClass() {
        $('.parties-map-menu h3.active').removeClass('active');
      }

      function partyInformation(e, data, html) {
        var party = data.filter(function(el) {
          return el.iso === e.features[0].properties.ISO3CD;
        });

        if (party.length !== 0) {
          if (party[0]['name']) {
            html += Drupal.t('Party name') + ': ' + party[0]['name'] + "<br/>";
          }
          if (party[0]['description']) {
            html += party[0]['description'] + "<br/>";
          }
        }

        return html;
      }

      function pointInformation(row, html) {
        if (row['name']) {
          html += '<span class="stscod5">' + Drupal.t('Party name') + ': ' + row['name'] + "</span><br/>";
        }
        if (row['description']) {
          html += row['description'] + "<br/>";
        }

        return html;
      }

      function hexToRgb(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);

        return result ? 'rgb('+
          parseInt(result[1], 16)+
          ','+
          parseInt(result[2], 16)+
          ','+
          parseInt(result[3], 16)+')'
          : null;
      }

    }
  };
}(jQuery, Drupal, drupalSettings));
