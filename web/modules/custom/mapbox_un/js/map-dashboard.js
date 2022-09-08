(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mapboxUn = {
    attach: function attach(context) {
      $('.parties-map-overview').once('partiesMap').each(function () {

        // Find and color parties
        var matchExpression = ['match', ['get', 'iso3']];
        var data = drupalSettings.mapbox_un.parties;
        var coords = drupalSettings.mapbox_un.coordinates;
        if (data != null && data.length > 0) {
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
        } else {
          matchExpression.push('rgb(165, 163, 157)');
          matchExpression.push('rgb(165, 163, 157)');
        }
        matchExpression.push('rgb(165, 163, 157)');

        let map = new mapboxgl.Map({
          container: 'parties-map-overview',
          // attributionControl: false,
          hash: false,
          // renderWorldCopies: false,
          maxZoom: 3,
          zoom: 0.8,
          center: [0, 10],
          style: {
            version: 8,
            sources: {
              polygons: {
                data: drupalSettings.mapbox_un.module_path + '/data/polygons.json',
                type: 'geojson'
              },
              lines: {
                data: drupalSettings.mapbox_un.module_path + '/data/lines.json',
                type: 'geojson'
              }
            },
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
                id: 'countries-all',
                type: 'fill',
                source: 'polygons',
                paint: {
                  'fill-color': matchExpression,
                  'fill-opacity': 0.8,
                },
                filter: ['!=', ['get', 'iso3'], null]
              },
              {
                id: 'countries-lines',
                type: 'line',
                source: 'lines',
                paint: {
                  'line-color': '#fff',
                  'line-width': 1,
                },
                filter: ['any', ['==', ['get', 'bdytyp'], 1], ['==', ['get', 'bdytyp'], 99]]
              },
              {
                id: 'countries-lines-dashed',
                type: 'line',
                source: 'lines',
                paint: {
                  'line-color': '#fff',
                  'line-width': 2,
                  'line-dasharray': [6, 6],
                },
                filter: ['==', ['get', 'bdytyp'], 3]
              },
              {
                id: 'countries-lines-dotted',
                type: 'line',
                source: 'lines',
                paint: {
                  'line-color': '#fff',
                  'line-width': 2,
                  'line-dasharray': [0.5, 0.5],
                },
                filter: ['==', ['get', 'bdytyp'], 4]
              },
              {
                id: 'hide_ata',
                type: 'fill',
                source: 'polygons',
                maxZoom: 2,
                minzoom: 0,
                filter: ['==', 'iso3', 'ATA'],
                paint: {
                  'fill-color': ['rgb', 255, 255, 255],
                  'fill-opacity': 1
                }
              },
            ]
          }
        });

        if (coords != null && coords.length > 0) {
          coords.forEach(function (row) {
            var default_color = 'rgb(232,188,5)';
            var color = row['color'];
            let marker;
            const coordinate = [row['long'], row['lat']];

            if (color) {
              var rgb_color = hexToRgb(color);
              marker = new mapboxgl.Marker({color: rgb_color})
                .setLngLat(coordinate)
                .setPopup(
                  new mapboxgl.Popup({closeButton: false, closeOnClick: true, offset: 25})
                    .setHTML(pointInformation(row, ''))
                )
                .addTo(map);
            } else {
              marker = new mapboxgl.Marker({color: default_color})
                .setLngLat(coordinate)
                .setPopup(
                  new mapboxgl.Popup({closeButton: false, closeOnClick: true, offset: 25})
                    .setHTML(pointInformation(row, ''))
                )
                .addTo(map);
            }
          });
        }

        map.on('load', ()=> {
          map.addControl(new mapboxgl.NavigationControl())
          map.resize();
        });

        var popup = new mapboxgl.Popup({
          closeButton: false,
          closeOnClick: true
        });

        map.on('click', 'countries-all', function(e) {
          if (data != null && data.length > 0) {
            var is_party = data.filter(function (el) {
              return el.iso === e.features[0].properties.iso3;
            });

            var layers = map.getStyle().layers;
            var current_country_layer = layers.filter(function (el) {
              return el.id === 'current_country'
            });

            if (is_party.length > 0) {
              if (typeof current_country_layer !== "undefined" && current_country_layer.length <= 0) {
                map.addLayer(
                  {
                    id: 'current_country',
                    type: 'fill',
                    source: 'polygons',
                    maxzoom: 4,
                    minzoom: 0,
                    filter: [
                      'all',
                      ['==', 'iso3', e.features[0].properties.iso3]
                    ],
                    paint: {
                      "fill-color": "#00757D",
                    }
                  },
                );
              }
              map.getCanvas().style.cursor = 'pointer';
              // a temporary fix for issue #11129
              if (e.features[0].properties.iso3 === 'TWN' || e.features[0].properties.iso3 === "CHN") {
                map.setFilter('current_country', [
                  "in",
                  "iso3",
                  'CHN',
                  'TWN'
                ])
              } else {
                map.setFilter('current_country', ['all', ['==', 'iso3', e.features[0].properties.iso3]]);
              }
            }

            if (is_party.length > 0) {
              if (e.features[0].properties.iso3) {
                var html = "<span class='stscod1'></span>";
                html = partyInformation(e, data, html);

                popup
                  .setLngLat(e.lngLat)
                  .setHTML(html)
                  .addTo(map);
              }
              else {
                popup.remove();
                if (map.getLayer("current_country")) {
                  map.removeLayer("current_country");
                }
              }
            }
          }
        });

        map.on('mousemove', 'countries-all', function(e) {
          if (data != null && data.length > 0) {
            var is_party = data.filter(function (el) {
              return el.iso === e.features[0].properties.iso3;
            });

            var layers = map.getStyle().layers;
            var current_country_layer = layers.filter(function (el) {
              return el.id === 'current_country'
            });

            if (is_party.length > 0) {
              if (typeof current_country_layer !== "undefined" && current_country_layer.length <= 0) {
                map.addLayer(
                  {
                    id: 'current_country',
                    type: 'fill',
                    source: 'polygons',

                    maxzoom: 4,
                    minzoom: 0,
                    filter: [
                      'all',
                      ['==', 'iso3', e.features[0].properties.iso3]
                    ],
                    paint: {
                      "fill-color": "#00757D",
                    }
                  },
                );
              }
              map.getCanvas().style.cursor = 'pointer';
              // a temporary fix for issue #11129
              if (e.features[0].properties.iso3 === 'TWN' || e.features[0].properties.iso3 === "CHN") {
                map.setFilter('current_country', [
                  "in",
                  "iso3",
                  'CHN',
                  'TWN'
                ])
              } else {
                map.setFilter('current_country', ['all', ['==', 'iso3', e.features[0].properties.iso3]]);
              }
            }
          }
        });

        map.on('mouseleave', 'countries-all', function(e){
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
          // popup.remove();
        });

        // Fly to region
        // Global
        if($('#global').length) {
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
        }
        // Africa
        if($('#region-africa').length) {
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
        }

        // Asia
        if($('#region-asia').length) {
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
        }

        // Central and Eastern Europe
        if($('#region-europe').length) {
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
        }

        // Central and South America and the Caribbean
        if($('#region-latin_america').length) {
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
        }

        // North America
        if($('#region-north_america').length) {
          document.getElementById('region-north_america').addEventListener('click', function () {
            removeClass();
            $(this).addClass('active');
            map.flyTo({
              center: [
                -99.7084, 55.3804
              ],
              essential: true,
              zoom: 1.5
            });
          });
        }

        // Oceania
        if($('#region-oceania').length) {
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
        }

      });

      function removeClass() {
        $('.parties-map-menu h3.active').removeClass('active');
      }

      function partyInformation(e, data, html) {
        var party = data.filter(function(el) {
          return el.iso === e.features[0].properties.iso3;
        });

        if (party.length !== 0) {
          if (party[0]['name']) {
            var name = '<b>' + party[0]['name'] + '</b>';
            if (party[0]['link']) {
              name = '<a target="_blank" href="' + party[0]['link'] + '">' + name + '</a>';
            }
            html += name + "<br/>";
          }
          if (party[0]['description']) {
            html += party[0]['description'] + "<br/>";
          }
        }

        return html;
      }

      function pointInformation(row, html) {
        if (row['name']) {
          var name = row['name'];
          if (row['link']) {
            name = '<a target="_blank" href="' + row['link'] + '">' + name + '</a>';
          }
          html += '<span class="stscod5"><b>' + name + "</b></span><br/>";
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
