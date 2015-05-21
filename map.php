<?php
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

    if (array_key_exists("tcx", $_GET)) {
        $tcxCode = $_GET['tcx'];
    } else {
        $tcxCode = "221144036";
    }

    $json = file_get_contents("http://nxfifteen.me.uk/api/fitbit/json.php?user=269VLG&data=ActivityTCX&tcx=" . $tcxCode);
    $json = json_decode($json);
?>
<html>
  <head>
    <title>leaflet-gpx demo</title>
    <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.5/leaflet.css" />
    <style type="text/css">
      body { width: 800px; margin: 0 auto; }
      .gpx { border: 5px #aaa solid; border-radius: 5px;
        box-shadow: 0 0 3px 3px #ccc;
        width: 800px; margin: 1em auto; }
      .gpx header { padding: 0.5em; }
      .gpx h3 { margin: 0; padding: 0; font-weight: bold; }
      .gpx .start { font-size: smaller; color: #444; }
      .gpx .map { border: 1px #888 solid; border-left: none; border-right: none;
        width: 800px; height: 300px; margin: 0; }
      .gpx footer { background: #f0f0f0; padding: 0.5em; }
      .gpx ul.info { list-style: none; margin: 0; padding: 0; font-size: smaller; }
      .gpx ul.info li { color: #666; padding: 2px; display: inline; }
      .gpx ul.info li span { color: black; }
    </style>
  </head>
  <body>
    <section id="demo" class="gpx" data-gpx-source="<?php echo $json->results->gpx; ?>" data-map-target="demo-map">
      <header>
        <h3>Loading...</h3>
        <span class="start"></span>
      </header>

      <article>
          <div style="width:100%; height:20px">
              <a href="http://nxfifteen.me.uk/api/fitbit/map.php?tcx=221144036">221144036</a> |
              <a href="http://nxfifteen.me.uk/api/fitbit/map.php?tcx=221505177">221505177</a> |
              <a href="http://nxfifteen.me.uk/api/fitbit/map.php?tcx=222440711">222440711</a> |
              <a href="http://nxfifteen.me.uk/api/fitbit/map.php?tcx=223337286">223337286</a>
          </div>

        <div style="clear: both;" class="map" id="demo-map"></div>

      </article>

      <footer>
        <ul class="info">
          <li>Distance:&nbsp;<span class="distance"></span>&nbsp;mi</li>
          &mdash; <li>Duration:&nbsp;<span class="duration"></span></li>
          &mdash; <li>Pace:&nbsp;<span class="pace"></span>/mi</li>
          &mdash; <li>Avg&nbsp;HR:&nbsp;<span class="avghr"></span>&nbsp;bpm</li>
          &mdash; <li>Elevation:&nbsp;+<span class="elevation-gain"></span>&nbsp;ft,
            -<span class="elevation-loss"></span>&nbsp;ft
            (net:&nbsp;<span class="elevation-net"></span>&nbsp;ft)</li>
        </ul>
      </footer>
    </section>

    <script src="http://cdn.leafletjs.com/leaflet-0.5/leaflet.js"></script>
    <script src="https://rawgithub.com/mpetazzoni/leaflet-gpx/master/gpx.js"></script>
    <script type="application/javascript">
      function display_gpx(elt) {
        if (!elt) return;

        var url = elt.getAttribute('data-gpx-source');
        var mapid = elt.getAttribute('data-map-target');
        if (!url || !mapid) return;

        function _t(t) { return elt.getElementsByTagName(t)[0]; }
        function _c(c) { return elt.getElementsByClassName(c)[0]; }

        var map = L.map(mapid);
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: 'Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        new L.GPX(url, {
          async: true,
          marker_options: {
            startIconUrl: 'http://github.com/mpetazzoni/leaflet-gpx/raw/master/pin-icon-start.png',
            endIconUrl:   'http://github.com/mpetazzoni/leaflet-gpx/raw/master/pin-icon-end.png',
            shadowUrl:    'http://github.com/mpetazzoni/leaflet-gpx/raw/master/pin-shadow.png',
          },
        }).on('loaded', function(e) {
          var gpx = e.target;
          map.fitBounds(gpx.getBounds());

          _t('h3').textContent = gpx.get_name();
          _c('start').textContent = gpx.get_start_time().toDateString() + ', '
            + gpx.get_start_time().toLocaleTimeString();
          _c('distance').textContent = gpx.get_distance_imp().toFixed(2);
          _c('duration').textContent = gpx.get_duration_string(gpx.get_moving_time());
          _c('pace').textContent     = gpx.get_duration_string(gpx.get_moving_pace_imp(), true);
          _c('avghr').textContent    = gpx.get_average_hr();
          _c('elevation-gain').textContent = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);
          _c('elevation-loss').textContent = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);
          _c('elevation-net').textContent  = gpx.to_ft(gpx.get_elevation_gain()
            - gpx.get_elevation_loss()).toFixed(0);
        }).addTo(map);
      }

      display_gpx(document.getElementById('demo'));
    </script>
  </body>
</html>
