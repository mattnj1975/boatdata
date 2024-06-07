@extends('layouts.admin')
@section('title', 'Trips')
@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        h1 {
            padding-top: 8px;
        }

        header .nav li a {
            padding-left: 5px;
            padding-right: 5px;
        }

        header .nav-link {
            color: #fff;
        }

        header .nav-link:hover {
            color: #54afe6;
        }

        header .nav-link.current {
            color: #54afe6;
        }

        header .nav-link i {
            font-size: 1.5em;
            padding: 5px 0 5px 0;
        }

        h2 {
            font-size: 1.9rem;
        }

        .month_selector i {
            font-size: 2rem;
            margin-top: 3px;
            padding: 0px 5px;
            color: #ccc;
        }

        .month_selector a i {
            color: rgba(33, 37, 41, 0.9);
        }

        .day_name,
        #trips thead th {
            background-color: rgba(33, 37, 41, 0.9);
            color: #fff;
            text-align: center;
            padding: 10px 0;
            font-weight: bold;
            line-height: normal;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        #trips thead th {
            text-align: left;
            padding-left: 5px;
        }

        .day_num {
            background-color: #fff;
            color: #666;
            text-align: center;
            padding: 10px 0;
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        .has_trip span {
            background-color: #54afe6;
            color: #fff;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;

        }

        .day_num.ignore {
            color: #CCC;
        }

        .viewLink {
            color: #999;
            cursor: pointer;
        }

        .viewLink:hover {
            color: rgb(33, 37, 31);
        }

        .highlight td {
            background-color: #54afe6;
            color: #fff;
        }

        .highlight td .viewLink {
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
        }

        .highlight td .viewLink:hover {
            color: #fff;
        }

        .leaflet-container {
            height: 600px;
            width: 800px;
            max-width: 100%;
            max-height: 100%;
        }
        
        
        .chart--container {
        height: 100%;
        width: 100%;
        min-height: 400px;
        }
        
        .zc-ref {
        display: none;
        }
    </style>
@endsection
@section('content')
    <div class="w-100">
        <div class="row">
            <h2 class="col-8"><?= date('F Y', strtotime($date)) ?></h2>
            <input type="hidden" id="hidden-date">
            <input type="hidden" id="hidden-mac">

            <div class="col-4 month_selector text-end">
                <a href="{{ route('master.myTrips', ['uid' => '', 'date' => date('Y-m-d', strtotime($date . ' -1 month'))]) }}"><i class="fa fa-chevron-left"></i></a>
                @if (date('Y-m', strtotime($date)) != date('Y-m'))
                    <a href="{{ route('master.myTrips', ['uid' => '', 'date' => date('Y-m-d', strtotime($date . ' +1 month'))]) }}"><i class="fa fa-chevron-right"></i></a>
                @else
                    <i class="fa fa-chevron-right"></i>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 px-4 col-sm-12 col-md-12">
            <?= $calendar ?>
        </div>
        <div class="col-lg-8 px-4 pt-4 pt-md-0 ps-md-1 col-sm-12 col-md-12">
            <table id="trips" class="display">
                <thead>
                    <tr>
                        <th style="min-width:80px;max-width:80px;">Date</th>
                        <th>Boat (mac)</th>
                        <th style="min-width:50px;max-width:50px;">Start</th>
                        <th style="min-width:50px;max-width:50px;">Finish</th>
                        <th style="min-width:90px;max-width:90px;">Duration</th>
                        <th style="min-width:90px;max-width:90px;">Distance</th>
                        <th style="max-width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $row = 0; @endphp
                    @foreach($trips as $trip)
                        @php $row++; @endphp
                        <tr data-date='{{ date('Y-m-d', strtotime($trip['TripDate'])) }}' data-mac='{{ $trip['mac'] }}' class="date_{{ date('Y-m-d', strtotime($trip['TripDate'])) }}">
                            <td>{{ date('d/m/Y', strtotime($trip['TripDate'])) }}</td>
                            <td>{{ (strlen($trip['boatname']) > 1 ? $trip['boatname'] . ' (' . $trip['mac'] . ')' : '-- (' . $trip['mac'] . ')') }}</td>
                            <td>{{ date('H:i', strtotime($trip['TripDate'] . ' ' . $trip['Begin'])) }}</td>
                            <td>{{ date('H:i', strtotime($trip['TripDate'] . ' ' . $trip['Finish'])) }}</td>
                            <td>{{ gmdate("H\h i\m", round((strtotime($trip['TripDate'] . ' ' . $trip['Finish']) - strtotime($trip['TripDate'] . ' ' . $trip['Begin'])) / 60) * 60) }}</td>
                            <td>{{ $trip['Trip'] }}</td>
                            <td style="max-width:60px;padding-left:0;"><i class="fa fa-map viewLink viewTrack" title="Track"></i> &nbsp;&nbsp;&nbsp;<i class="fa fa-table viewLink viewData" title="Data"></i></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs d-none" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="track-tab" data-bs-toggle="tab" data-bs-target="#track-tab-pane" type="button" role="tab" aria-controls="track-tab-pane" aria-selected="true">Track</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="speed-tab" data-bs-toggle="tab" data-bs-target="#speed-tab-pane" type="button" role="tab" aria-controls="speed-tab-pane" aria-selected="true">Speed</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="engine-tab" data-bs-toggle="tab" data-bs-target="#engine-tab-pane" type="button" role="tab" aria-controls="engine-tab-pane" aria-selected="true">Engine</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="log-tab" data-bs-toggle="tab" data-bs-target="#log-tab-pane" type="button" role="tab" aria-controls="log-tab-pane" aria-selected="false">Log</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-tab-pane" type="button" role="tab" aria-controls="data-tab-pane" aria-selected="false">Data</button>
                </li>

            </ul>
            <div class="tab-content" id="tripData">
                <div class="tab-pane fade" id="track-tab-pane" role="tabpanel" aria-labelledby="track-tab" tabindex="0" style="padding-top:15px;">
                    <div id="map" style="width: 100%; height: 600px;"></div>
                    <div class="d-none" id="no-data-alert">No position data available</div>
                </div>
                <div class="tab-pane fade" id="data-tab-pane" role="tabpanel" aria-labelledby="data-tab" tabindex="0" style="padding-top:15px;">
                    <table id="data" class="display table table-bordered  table-hover dt-responsive display nowrap">
                        <thead>
                            <tr>
                                <th style="min-width:50px;max-width:50px;">Time (UTC)</th>
                                <th style="">Vessel <br />Position</th>
                                <th style="min-width:55px;max-width:55px;">SOG</th>
                                <th style="min-width:60px;max-width:60px;">COG</th>
                                <th style="min-width:50px;max-width:50px;">Depth</th>
                                <th style="min-width:65px;max-width:65px;">Heading</th>
                                <th style="min-width:50px;max-width:50px;">Speed</th>
                                <th style="min-width:70px;max-width:70px;">Port RPM</th>
                                <th style="min-width:75px;max-width:75px;">Port Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">Stb RPM</th>
                                <th style="min-width:75px;max-width:75px;">Stb Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">App Wind</th>
                                <th style="min-width:70px;max-width:70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="trip-data-table-body">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="speed-tab-pane" role="tabpanel" aria-labelledby="speed-tab" tabindex="0" style="padding-top:15px;">
                    <div class="full-height chart--container" id='SpeedChart'></div>
                    <button class='btn btn-info' id="demo1">Export</button>

                    <div id="displayEvents">
                        <p id="output">&nbsp;</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <table id="dataLog" class="display">
                        <thead>
                            <tr>
                                <th style="min-width:50px;max-width:50px;">Time (UTC)</th>
                                <th style="">Vessel <br />Position99</th>
                                <th style="min-width:55px;max-width:55px;">SOG</th>
                                <th style="min-width:60px;max-width:60px;">COG</th>
                                <th style="min-width:50px;max-width:50px;">Depth</th>
                                <th style="min-width:65px;max-width:65px;">Heading</th>
                                <th style="min-width:50px;max-width:50px;">Speed</th>
                                <th style="min-width:70px;max-width:70px;">Port RPM</th>
                                <th style="min-width:75px;max-width:75px;">Port Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">Stb RPM</th>
                                <th style="min-width:75px;max-width:75px;">Stb Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">App Wind</th>
                                <th style="min-width:70px;max-width:70px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="log-data-table-body">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="engine-tab-pane" role="tabpanel" aria-labelledby="engine-tab" tabindex="0" style="padding-top:15px;">
                    <div class="full-height chart--container" id='EngineChart'></div>
                    <button class='btn btn-info' id="demo2">Export</button>

                    <div id="displayEvents">
                        <p id="output">&nbsp;</p>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="logData">
                <div class="tab-pane fade" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <div class="d-none" id="no-data-alert">No position data available</div>
                </div>
                <div class="tab-pane fade" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <table id="data" class="display">
                        <thead>
                            <tr>
                                <th style="min-width:50px;max-width:50px;">Time (UTC)</th>
                                <th style="">Vessel <br />Position99</th>
                                <th style="min-width:55px;max-width:55px;">SOG</th>
                                <th style="min-width:60px;max-width:60px;">COG</th>
                                <th style="min-width:50px;max-width:50px;">Depth</th>
                                <th style="min-width:65px;max-width:65px;">Heading</th>
                                <th style="min-width:50px;max-width:50px;">Speed</th>
                                <th style="min-width:70px;max-width:70px;">Port RPM</th>
                                <th style="min-width:75px;max-width:75px;">Port Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">Stb RPM</th>
                                <th style="min-width:75px;max-width:75px;">Stb Fuel Rate</th>
                                <th style="min-width:70px;max-width:70px;">App Wind</th>
                            </tr>
                        </thead>
                        <tbody id="log-data-table-body">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>


<script src="https://kit.fontawesome.com/3b7976185b.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.7/sorting/datetime-moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.7/api/row().show().js" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script type="text/javascript">
        let dataTable;
        $(document).ready(function() {
            document.getElementById("speed-tab").onclick = loadSpeed;
            document.getElementById("engine-tab").onclick = loadEngine;
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('body').on('click', '.deleteData', function () {
     
                var data_id = $(this).data("id");
               
                const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success m-2",
                            cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    swalWithBootstrapButtons.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Delete it!",
                        cancelButtonText: "No , Cancel it",
                        reverseButtons: true ,
                        allowOutsideClick: false, 
                        showLoaderOnConfirm: true, 
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url: "/view/delete_boat_data"+'/'+ data_id,
                            success: function (data) {
                                loadTable();
                                loadLog();
                                Toast.fire({
                                    icon: 'success',
                                    title: data.success
                                });
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        // Handle cancellation if needed
                    }
                });
        
            }); 

            



            $.fn.dataTable.moment('DD/MMM/YYYY');

            const tripsTable = $('#trips').DataTable({
                dom: 'rtip',
                "pageLength": 10,
                "columnDefs": [{
                    "targets": [2, 3, 6],
                    "orderable": false
                }]
            });
            $('#trips').addClass('table-responsive');

            $(".has_trip span").on("click", function() {
                var tdate = $(this).attr("data-date");
                var tripTable = $('#trips').DataTable();
                tripTable.rows().nodes().to$().removeClass('highlight');
                tripTable.rows(".date_" + tdate).nodes().to$().addClass('highlight');
                tripTable.row(".date_" + tdate).show().draw(false);
            });

            tripsTable.on('draw.dt', function() {

                $(".viewData").on("click", function() {

                    $("#map").removeClass("d-none")

                    $("#data-tab-pane").removeClass('active').removeClass('show')
                    $("#data-tab").removeClass('active')

                    $("#myTab").removeClass('d-none')

                    row = $(this).closest('tr');
                    let date = row.attr('data-date')
                    let mac = row.attr('data-mac')

                    $("#hidden-date").val(date)
                    $("#hidden-mac").val(mac)

                    $("body").attr("data-loading", true)
                    $("body").attr("map-loading", true)

                    $("#data-tab").click()
                });

                $(".viewTrack").on("click", function() {

                    $("#map").removeClass("d-none")

                    $("#track-tab-pane").removeClass('active').removeClass('show')
                    $("#track-tab").removeClass('active')

                    $("#myTab").removeClass('d-none')

                    row = $(this).closest('tr');
                    let date = row.attr('data-date')
                    let mac = row.attr('data-mac')

                    $("#hidden-date").val(date)
                    $("#hidden-mac").val(mac)

                    $("body").attr("map-loading", true)
                    $("body").attr("data-loading", true)


                    $("#track-tab").click()
                });

            })

            $("#track-tab").on("shown.bs.tab", loadMap)

            $("#log-tab").on("shown.bs.tab", loadLog)

            $("#data-tab").on("shown.bs.tab", loadTable)

            $("#graph-tab").on("shown.bs.tab", loadChart)

            tripsTable.draw()

        })

        //////////////////////////////////////////////////////////////////
        // MAP STUFF

        // TRACK VARIABLES
        var TrackStyle = {
            "color": "#ff007b",
            "weight": 3,
            "opacity": 0.85
        };
        var geojsonMarkerOptions = {
            radius: 3,
            fillColor: "#ff7800",
            color: "#000",
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };
        var greenIcon = L.icon({
            iconUrl: '/view/assets/img/map/dot.png',
            iconSize: [10, 10],
            // size of the icon
            popupAnchor: [1, 1] // point from which the popup should open relative to the iconAnchor
        });


        // BUILD THE MAP MODULE
        map = L.map('map');

        function loadMap() {

            let coordinates = []
            let markers = []

            if ($("body").is("[map-loading]")) {

                let date = $("#hidden-date").val()
                let mac = $("#hidden-mac").val()
                $.post("{{route('admin.getTrackData')}}", {
                    date: date,
                    mac: mac
                }, function(data) {
                    // console.log(data)
                    // data = JSON.parse(data)
                    console.log(data)

                    map.eachLayer((layer) => {
                        layer.remove();
                    });

                    data.points.forEach(function(item, index) {
                        console.log(item)
                        if (Math.abs(item.lon) != 0 && Math.abs(item.lat) != 0) {
                            coordinates.push([item.lon, item.lat])
                        }

                        let timeSinceLastMarker

                        if (index == 0) {
                            markers.push(item);
                        } else {
                            currentPointTime = item.time;
                            lastMarkerTime = markers[markers.length - 1].time;
                            timeSinceLastMarker = Math.abs(new Date('2011/10/09 ' + currentPointTime) - new Date('2011/10/09 ' + lastMarkerTime));
                            if (timeSinceLastMarker >= 300000) {
                                markers.push(item)
                            }
                        }

                    })
                    console.log(markers)

                    console.log(coordinates)

                       osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                    oSeamUrl='https://tiles.openseamap.org/seamark/{z}/{x}/{y}.png';
	                osmAttrib='Map data © <a href="http://www.openseamap.org">OpenSeaMap</a> contributors';
	                
                    osm = new L.TileLayer(osmUrl, {
                        minZoom: 4,
                        maxZoom: 20,
                        attribution: osmAttrib
                    });
                    oSeam = new L.TileLayer(oSeamUrl, {
                        minZoom: 4, 
                        maxZoom: 20,
                    });	
                    
                    map.addLayer(osm);
                    map.addLayer(oSeam);

                    if (coordinates.length > 0) {
                        myTRIP = [{
                            "type": "LineString",
                            "coordinates": coordinates
                        }];
                        feature = L.geoJSON(myTRIP, {
                            style: TrackStyle
                        }).addTo(map);
                        map.fitBounds(feature.getBounds(), {
                            padding: [5, 5]
                        });

                        for (var i = 0; i < markers.length; i++) {
                            var lon = markers[i]['lon'];
                            var lat = markers[i]['lat'];

                            var popupText;

                            var popupText =
                                "Time (UTC): <b>" + markers[i]['time'] + "</b>"

                            if (markers[i]['sog'] != "-") {
                                popupText += "<br>SOG: <b>" + markers[i]['sog'] + " kts</b>"
                            }

                            if (markers[i]['cog'] != "-") {
                                popupText += " COG: <b>" + markers[i]['cog'] + "°</b>"
                            }

                            if (markers[i]['dep'] != "-") {
                                popupText += "<br>Depth: <b>" + markers[i]['dep'] + "m</b>"
                            }

                            if (markers[i]['spd'] != "-") {
                                popupText += " Speed: <b>" + markers[i]['spd'] + " kts</b>"
                            }

                            if (markers[i]['hdg'] != "-") {
                                popupText += "<br>Heading: <b>" + markers[i]['hdg'] + "° </b>"
                            }

                            if (markers[i]['rpm1'] != "-") {
                                popupText += "<br>Engine 1 RPM: <b>" + markers[i]['rpm1'] + "rpm</b>"
                            }

                            if (markers[i]['fuelr1'] != "-") {
                                popupText += "<br>Engine 1 Fuel Rate: <b>" + markers[i]['fuelr1'] + "</b>"
                            }

                            if (markers[i]['rpm2'] != "-") {
                                popupText += "<br>Engine 2 RPM: <b>" + markers[i]['rpm2'] + "rpm</b>"
                            }

                            if (markers[i]['fuelr2'] != "-") {
                                popupText += "<br>Engine 2 Fuel Rate: <b>" + markers[i]['fuelr2'] + "</b>"
                            }

                            if (markers[i]['aws'] != "-") {
                                popupText += "<br>Apparent Wind: <b>" + markers[i]['aws'] + " kts @ " + markers[i]['awa'] + "°</b>"
                            }


                            var markerLocation = new L.LatLng(lat, lon);
                            var marker = new L.Marker(markerLocation, {
                                icon: greenIcon
                            });
                            map.addLayer(marker);
                            marker.bindPopup(popupText);
                        }

                        // SPECIAL MAX VALUE MARKERS
                        // STYLING
                        const TripIcon = L.Icon.extend({
                            options: {
                                shadowUrl: '/view/assets/img/map/shadow.png',
                                iconSize: [32, 37],
                                shadowSize: [41, 41],
                                iconAnchor: [16, 37],
                                shadowAnchor: [5, 42],
                                popupAnchor: [4, -38]
                            }
                        });
                        startIcon = new TripIcon({
                                iconUrl: '/view/assets/img/map/start.png'
                            }),
                            endIcon = new TripIcon({
                                iconUrl: '/view/assets/img/map/end.png'
                            }),
                            mindepthIcon = new TripIcon({
                                iconUrl: '/view/assets/img/map/mindepth.png'
                            });
                        speedIcon = new TripIcon({
                            iconUrl: '/view/assets/img/map/speed.png'
                        });
                        sogIcon = new TripIcon({
                            iconUrl: '/view/assets/img/map/sog.png'
                        });
                        awindIcon = new TripIcon({
                            iconUrl: '/view/assets/img/map/wind.png'
                        });
                        twindIcon = new TripIcon({
                            iconUrl: '/view/assets/img/map/wind.png'
                        });
                        rpmIcon = new TripIcon({
                            iconUrl: '/view/assets/img/map/rpm.png'
                        });

                        // ACTUAL MARKERS
                        // START TRIP
                        L.marker([data.maxes.startTime.latdec, data.maxes.startTime.londec], {
                            icon: startIcon
                        }).addTo(map).bindPopup("Start Time: <b>" + data.maxes.startTime.utc + "</b>");
                        // END TRIP
                        if (data.maxes.distance > 0) {
                            L.marker([data.maxes.endTime.latdec, data.maxes.endTime.londec], {
                                icon: endIcon
                            }).addTo(map).bindPopup("End Time: <b>" + data.maxes.endTime.utc + "</b><br>Distance:<b> " + data.maxes.distance + "nm</b><br>GPS Dist:<b> " + data.maxes.gpsdist / 1852 + "nm<br></b>");
                        } else {
                            L.marker([data.maxes.endTime.latdec, data.maxes.endTime.londec], {
                                icon: endIcon
                            }).addTo(map).bindPopup("End Time: <b>" + data.maxes.endTime.utc + "</b>");

                        }
                        // MAX SPEED
                        L.marker([data.maxes.speed.latdec, data.maxes.speed.londec], {
                            icon: speedIcon
                        }).addTo(map).bindPopup("Max Speed: <b>" + data.maxes.speed.spd + " kts</b><br>Time: <b>" + data.maxes.speed.utc + "</b>");
                        // MAX SOG
                        L.marker([data.maxes.sog.latdec, data.maxes.sog.londec], {
                            icon: sogIcon
                        }).addTo(map).bindPopup("Max SOG: <b>" + data.maxes.sog.sog + " kts</b><br>Time: <b>" + data.maxes.sog.utc + "</b>");

                        // MIN DEPTH
                        L.marker([data.maxes.minDepth.latdec, data.maxes.minDepth.londec], {
                            icon: mindepthIcon
                        }).addTo(map).bindPopup("Depth Min: <b>" + data.maxes.minDepth.dep + "m</b><br>Time: <b>" + data.maxes.minDepth.utc + "</b>");
                        // MAX APP WIND SPEED
                        L.marker([data.maxes.awind.latdec, data.maxes.awind.londec], {
                            icon: awindIcon
                        }).addTo(map).bindPopup("AWS Max: <b>" + data.maxes.awind.aws + " kts @" + data.maxes.awind.awa + "°</b><br>Time (UTC): <b>" + data.maxes.awind.utc + "</b>");
                        // MAX TRUE WIND SPEED
                        L.marker([data.maxes.twind.latdec, data.maxes.twind.londec], {
                            icon: twindIcon
                        }).addTo(map).bindPopup("TWS Max: <b>" + data.maxes.twind.aws + " kts @" + data.maxes.twind.awa + "°</b><br>Time (UTC): <b>" + data.maxes.twind.utc + "</b>");


                        // //awind icon
                        // L.marker([50.77067057, -1.27299995], {icon: awindIcon}).addTo(map).bindPopup("AWS Max:<b>29.8knots</b><br>AWA:<b>359&deg</b><br>TWS:<b>26.0knots</b><br>TWA:<b>347.2&deg</b><br>Time:<b>16:04:14 utc</b>");
                        // //sog icon
                        //L.marker([50.77150065, -1.26133334], {icon: sogIcon}).addTo(map).bindPopup("SOG Max:<b>8.57knots</b><br>Time:<b>16:00:53</b>");
                        var popup = L.popup();

                    }



                    $("body").removeAttr("map-loading")

                    if (coordinates.length == 0) {
                        $("#map").addClass("d-none")
                        $("#no-data-alert").removeClass('d-none')
                    } else {
                        $("#map").removeClass("d-none")
                        $("#no-data-alert").addClass('d-none')
                    }

                    map.invalidateSize();


                });
            }


        }

        function loadLog() {

            // if ($("body").is("[data-loading]")) {

                let date = $("#hidden-date").val()
                let mac = $("#hidden-mac").val()

                $.post("{{route('admin.getLogData')}}", {
                    start: date,
                    end: date,
                    mac: mac
                }, function(data) {
                    $('#dataLog').DataTable().destroy();
                    
                    $('#log-data-table-body').html(data)
                    dataTable = $('#dataLog').DataTable({
                        dom: 'frtip',
                        "pageLength": 10,

                    });
                    $('#dataLog').addClass('table-responsive');
                });

                $("body").removeAttr("data-loading")
            // }

        }


        function loadTable() {
            // if ($("body").is("[data-loading]")) {
                let date = $("#hidden-date").val();
                let mac = $("#hidden-mac").val();
        
                $.post("{{route('admin.getTableData')}}", {
                    date: date,
                    mac: mac
                }, function(data) {
                    $('#data').DataTable().destroy();
                    
                    // Initialize DataTable with received data
                    dataTable = $('#data').DataTable({
                        dom: 'frtip',
                        "pageLength": 10,
                        data: data, // Assign the received data directly
                        columns: [
                            { data: 'utc' },
                            { data: 'location' },
                            { data: 'sog' },
                            { data: 'cog' },
                            { data: 'depth' },
                            { data: 'heading' },
                            { data: 'speed' },
                            { data: 'rpm1' },
                            { data: 'fuelr1' },
                            { data: 'rpm2' },
                            { data: 'fuelr2' },
                            { data: 'awa' },
                            { data: 'delete_button' },
                        ]
                    });
                    
                    $('#data').addClass('table-responsive');
                    $('#data').css('width', 'auto');
                });
        
                $("body").removeAttr("data-loading");
            // }
        }
        function loadSpeed() {
            let date = $("#hidden-date").val()
            let mac = $("#hidden-mac").val()

            $.post("{{route('admin.fetchSpeed')}}", {
                start: date,
                end: date,
                uid: mac
            }, function(data) {
                
                console.log(data)
                
                let myConfig = {  

                graphset: [{
                type: 'line',  
                height: '98%',
                width: '98%',
                /* Position your chart using x/y attributes */
                x: '1%',
                y: '1%',
                
                'crosshair-x': {
                    shared: true
                },
                
                title: {      
                text: 'Wind Speed, Boat Speed and SOG',      
                fontSize: 16,   
                },   
                legend: {   
                visible: true, 
                }, 
                scaleX: {     
                step: "10minute",
                    transform: {
                    type: "date",
                    all: "%d/%m/%y<br>%H:%i:%s"
                    },
                
                zooming: true, 
                label: { text: 'Time' },      
                },    
                
                scaleY: {  
                zooming: true,  
                // Scale label with unicode character  
                label: { text: 'Wind Speed (kts)' }  

                },    

                scaleY2: {      
                // Scale label with unicode character  
                label: { text: 'Boat Speed (kts)' }    
                },    
                scaleY3: {      
                // Scale label with unicode character  
                label: { text: 'Velocity Made Good (kts)' }    
                },  
                
                series: [{   
                    

                    // Plot 1 values, linear data     
                    scales: "scaleX,scaleY",
                    values: data.myAWS, 
                    decimals: 1,
                    text: 'App Wind Speed',     
                    },   
                    {
                    // Plot 2 values, linear data     
                    scales: "scaleX,scaleY",
                    values: data.myTWS, 
                    decimals: 1,
                    visible: false,
                    text: 'True Wind Speed',     
                    },   
                    {
                    // Plot 2 values, linear data     
                    scales: "scaleX,scaleY",
                    values: data.myGWS, 
                    decimals: 1,
                    visible: false,
                    text: 'Ground Wind Speed',     
                    },    
                    
                    {        
                    // Plot 3 values, linear data        
                    scales: "scaleX,scaleY2",
                    values: data.mySOG,  
                    decimals: 1,
                    visible: false,
                    text: 'Speed Over Ground'     
                    },
                    {       
                    // Plot 4 values, linear data     
                    scales: "scaleX,scaleY2",
                    values: data.mySPD,   
                    decimals: 1,
                    text: 'Boat Speed',     
                    }, 
                    {       
                    // Plot 5 values, linear data     
                    scales: "scaleX,scaleY3",
                    values: data.myVMG,  
                    decimals: 1,
                    visible: false, 
                    text: 'VMG',     
                    },  
                ]},
                
                
                ]};
                
                console.log(myConfig)
                zingchart.render({   
                    id: 'SpeedChart', 
                    height: '100%',
                    data: myConfig,  
                });  
                
                demo1.addEventListener('click', function() {
                    var dump = zingchart.exec('SpeedChart', 'downloadXLS');
                    document.getElementById('output').innerHTML = dump;
                });
            });
        }
        function loadEngine() {
            let date = $("#hidden-date").val();
            let mac = $("#hidden-mac").val();

            $.post("{{ route('admin.fetchEngine') }}", {
                start: date,
                end: date,
                uid: mac
            }, function(data) {
                console.log(data.myRPM1)
                let myConfig = {
                    graphset: [{
                        type: 'line',
                        height: '98%',
                        width: '98%',
                        x: '1%',
                        y: '1%',
                        'crosshair-x': {
                            shared: true
                        },
                        title: {
                            text: 'Engine 1 Data',
                            fontSize: 16,
                        },
                        legend: {
                            visible: true,
                        },
                        scaleX: {
                            step: "10minute",
                            transform: {
                                type: "date",
                                all: "%d/%m/%y<br>%H:%i:%s"
                            },
                            zooming: true,
                            label: { text: 'Time' },
                        },
                        scaleY: {
                            zooming: true,
                            label: { text: 'RPM' }
                        },
                        scaleY2: {
                            label: { text: 'Boat Speed (kts)', visible: false }
                        },
                        scaleY3: {
                            label: { text: 'Temp' }
                        },
                        scaleY4: {
                            label: { text: 'Fuel Rate', visible: false }
                        },
                        scaleY5: {
                            label: { text: 'Litres per NM', visible: false }
                        },
                        series: [
                            {   
                                scales: "scaleX,scaleY",
                                values: data.myRPM1, 
                                decimals: 0,
                                text: 'RPM',     
                            },   
                            {
                                scales: "scaleX,scaleY3",
                                values: data.myBOOST1, 
                                decimals: 1,
                                visible: false,
                                text: 'Boost Pressure',     
                            },   
                            {
                                scales: "scaleX,scaleY4",
                                values: data.myFUELR1, 
                                decimals: 1,
                                visible: false,
                                text: 'Fuel Rate',     
                            },    
                            {        
                                scales: "scaleX,scaleY2",
                                values: data.myLOAD1,  
                                decimals: 1,
                                visible: false,
                                text: 'Engine Load'     
                            }, 
                            {       
                                scales: "scaleX,scaleY2",
                                values: data.mySOG,   
                                decimals: 1,
                                text: 'SOG',     
                            }, 
                            {       
                                scales: "scaleX,scaleY5",
                                values: data.myECON1,   
                                decimals: 1,
                                text: 'Litres per NM',     
                            }, 
                            {       
                                scales: "scaleX,scaleY3",
                                values: data.myCOOLT1,  
                                decimals: 1,
                                visible: false, 
                                text: 'Coolant Temp',     
                            }
                        ]
                    }]
                };

                zingchart.render({
                    id: 'EngineChart',
                    height: '100%',
                    data: myConfig,
                });

                demo2.addEventListener('click', function() {
                    var dump = zingchart.exec('EngineChart', 'downloadXLS');
                    document.getElementById('output').innerHTML = dump;
                });
            });
        }

        function loadChart() {

            if ($("body").is("[data-loading]")) {



                $("body").removeAttr("data-loading")
            }

        }

    </script>
@endsection
