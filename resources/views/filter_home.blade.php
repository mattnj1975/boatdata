@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" type="text/css" href="login_assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
<style>
    body {
        background: linear-gradient(180deg, #071827 0%, #102f46 45%, #f4f7fa 45%);
        color: #102033;
    }

    .marine-hero {
        padding: 28px 0 24px;
        color: #fff;
    }

    .marine-title {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }
	
	#tableBody td {
    padding-top: 4px !important;
    padding-bottom: 4px !important;
    vertical-align: middle;
    font-size: 13px;
}

#tableBody tr {
    height: 34px;
}

    .marine-subtitle {
        color: #b8d4e8;
        margin-bottom: 0;
    }

    .marine-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 18px 45px rgba(0,0,0,.18);
        overflow: hidden;
    }

    .marine-card-header {
        background: #0b2538;
        color: #fff;
        padding: 14px 20px;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .marine-card-body {
        padding: 22px;
    }

    .marine-login-btn {
        border-color: rgba(255,255,255,.55);
        color: #fff;
        border-radius: 999px;
        padding: 8px 18px;
    }

    .marine-login-btn:hover {
        background: #fff;
        color: #0b2538;
    }

.trip-results-table {
    width: 100% !important;
    min-width: 900px;
    table-layout: auto;
    margin-bottom: 0;
}

.trip-results-table th,
.trip-results-table td {
    vertical-align: middle;
    padding: 18px 16px;
}

.trip-results-table thead th {
    background: #f3f7fb;
    border-bottom: 2px solid #d7e2eb !important;
    white-space: nowrap;
}

.trip-results-table tbody tr:hover {
    background: #f8fbfe;
}

.table-responsive {
    width: 100%;
    overflow-x: auto;
}

.pagination {
    margin-top: 0;
    margin-bottom: 0;
}		
		
    
</style>
@endsection
@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-center mb-2">
                <a class="btn btn-outline-secondary" href="{{route('dashboard')}}" style="width: 250px;">Login to Admin Panel</a>
            </div>
        </div>

    </div>
	@include('partials.boat_nav', ['mac' => $mac ?? null])


<div class="row">
    <div class="col-12">
        <div class="card marine-card">
            <div class="marine-card-header">
                <b>Boat Trips</b>

                <a href="../view"
                   style="color: white; font-weight: bold; font-size: 1.5rem; text-decoration: none; cursor: pointer;"
                   title="Back">
                    &times;
                </a>
            </div>

            <div class="marine-card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered trip-results-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Boat Name</th>
                                <th>Trip Date</th>
                                <th>Start</th>
                                <th>Finish</th>
                                <th>Duration</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>

                <div class="row justify-content-between align-items-center mt-3">
                    <div class="col-auto">
                        <div id="paginationContainer"></div>
                    </div>
                    <div class="col-auto">
                        <div id="entryCount"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="container-fluid boat_details" id="boat_details">
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
                                <th>Time (UTC)</th>
                                <th>Vessel Position</th>
                                <th>SOG</th>
                                <th>COG</th>
                                <th>Depth</th>
                                <th>Heading</th>
                                <th>Speed</th>
                                <th>Port RPM</th>
                                <th>Port Fuel Rate</th>
                                <th>Stb RPM</th>
                                <th>Stb Fuel Rate</th>
                                <th>App Wind</th>
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

                <div class="tab-pane fade" id="engine-tab-pane" role="tabpanel" aria-labelledby="engine-tab" tabindex="0" style="padding-top:15px;">
                    <div class="full-height chart--container" id='EngineChart'></div>
                    <button class='btn btn-info' id="demo2">Export</button>

                    <div id="displayEvents">
                        <p id="output">&nbsp;</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <table id="dataLog" class="display">
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
            <div class="tab-content" id="logData">
                <div class="tab-pane fade" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                    <div class="d-none" id="no-data-alert">No position data available</div>
                </div>
                <div class="tab-pane fade table table-responsive" id="log-tab-pane" role="tabpanel" aria-labelledby="log-tab" tabindex="0" style="padding-top:15px;">
                    <table id="data" class="display">
                        <thead>
                            <tr>
                                <th>Time (UTC)</th>
                                <th>Vessel Position</th>
                                <th>SOG</th>
                                <th>COG</th>
                                <th>Depth</th>
                                <th>Heading</th>
                                <th>Speed</th>
                                <th>Port RPM</th>
                                <th>Port Fuel Rate</th>
                                <th>Stb RPM</th>
                                <th>Stb Fuel Rate</th>
                                <th>App Wind</th>
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
</div>
<a name="map"></a>
<input type="hidden" id="hidden-date">
<input type="hidden" id="hidden-mac">
@endsection
@section('js')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentPage = 1;
        var rowsPerPage = 5;
        var data = {!! json_encode($Settings) !!}; // Assuming $Settings contains your data

        renderTable();

        function renderTable() {
            var startIndex = (currentPage - 1) * rowsPerPage;
            var endIndex = startIndex + rowsPerPage;
            var displayedData = data.slice(startIndex, endIndex);

            var tableBody = document.getElementById('tableBody');
            tableBody.innerHTML = '';

            displayedData.forEach(function(item) {
                var tripDate = new Date(item['TripDate']);
                var formattedTripDate = ('0' + tripDate.getDate()).slice(-2) + '/' + ('0' + (tripDate.getMonth() + 1)).slice(-2) + '/' + tripDate.getFullYear();

                var row = '<tr>' +
				'<td>' +
    '<div class="d-flex btn-group-lg align-items-center" role="group" style="gap:12px;">' +

      '<a href="javascript:void(0);" title="Show track below" style="color:#0d6efd;">' +
    '<i style="cursor:pointer;" class="fa fa-map viewLink viewTrack"></i>' +
'</a>' +

        
'<a href="/view/boat-ais/' + item['mac'] + '/' + item['TripDate'] + '" title="AIS Traffic" style="color:#0d6efd;">' +
    '<i class="fa fa-ship"></i>' +
'</a>' +

    '</div>' +
'</td>' +
'<td>' +
    '<strong>' + item['boatname'] + '</strong>' +
    '<small data-mac="' + item['mac'] + '" style="display:none;">' + item['mac'] + '</small>' +
'</td>' +
                            '<td data-date="' + formattedTripDate + '">' + formattedTripDate + '</td>' +
                            '<td>' + (item['Begin'] ? item['Begin'] : 'na') + '</td>' +
                            '<td>' + (item['Finish'] ? item['Finish'] : 'na') + '</td>';

                if (item['Begin'] && item['Finish']) {
                    var beginTime = new Date(item['TripDate'] + ' ' + item['Begin']);
                    var finishTime = new Date(item['TripDate'] + ' ' + item['Finish']);
                    var durationInMinutes = Math.round((finishTime - beginTime) / (1000 * 60));
                    var hours = Math.floor(durationInMinutes / 60);
                    var minutes = durationInMinutes % 60;
                    var duration = hours + 'h ' + minutes + 'm';
                    row += '<td>' + duration + '</td>';
                } else {
                    row += '<td>na</td>';
                }

                row += '<td>' + (item['Trip'] ? item['Trip'] : 'na') + '</td>' +
                        '</tr>';

                tableBody.innerHTML += row;
            });

            renderPagination();
            renderEntryCount();
        }

function renderPagination() {
    var totalPages = Math.ceil(data.length / rowsPerPage);

    var paginationHTML = '<nav aria-label="Page navigation"><ul class="pagination">';

    // Previous button
    paginationHTML += '<li class="page-item ' + (currentPage === 1 ? 'disabled' : '') + 
                      '"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>';

    if (totalPages <= 20) {
        // Show all pages if totalPages <= 20
        for (var i = 1; i <= totalPages; i++) {
            paginationHTML += '<li class="page-item ' + (i === currentPage ? 'active' : '') + 
                              '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
    } else {
        // Show first 3 pages always
        for (var i = 1; i <= 3; i++) {
            paginationHTML += '<li class="page-item ' + (i === currentPage ? 'active' : '') + 
                              '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }

        // Ellipsis if currentPage > 5 (means pages hidden after 3)
        if (currentPage > 5) {
            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Pages around the current page
        var startPage = Math.max(4, currentPage - 1);
        var endPage = Math.min(totalPages - 3, currentPage + 1);

        for (var i = startPage; i <= endPage; i++) {
            if (i > 3 && i < totalPages - 2) {
                paginationHTML += '<li class="page-item ' + (i === currentPage ? 'active' : '') + 
                                  '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
            }
        }

        // Ellipsis if currentPage < totalPages - 4 (means pages hidden before last 3)
        if (currentPage < totalPages - 4) {
            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Show last 3 pages always
        for (var i = totalPages - 2; i <= totalPages; i++) {
            paginationHTML += '<li class="page-item ' + (i === currentPage ? 'active' : '') + 
                              '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
        }
    }

    // Next button
    paginationHTML += '<li class="page-item ' + (currentPage === totalPages ? 'disabled' : '') + 
                      '"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';

    paginationHTML += '</ul></nav>';

    document.getElementById('paginationContainer').innerHTML = paginationHTML;

    // Add event listeners to pagination links
    document.querySelectorAll('.pagination .page-link').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var page = parseInt(this.getAttribute('data-page'));
            if (!isNaN(page) && page >= 1 && page <= totalPages) {
                currentPage = page;
                renderTable();
                renderPagination();
            }
        });
    });
}


        function renderEntryCount() {
            var totalCount = data.length;
            var fromIndex = Math.min((currentPage - 1) * rowsPerPage + 1, totalCount);
            var toIndex = Math.min((currentPage - 1) * rowsPerPage + rowsPerPage, totalCount);

            document.getElementById('entryCount').innerHTML = 'Showing ' + fromIndex + ' to ' + toIndex + ' of ' + totalCount + ' entries';
        }
    });

    $(document).ready(function() {
        document.getElementById("speed-tab").onclick = loadSpeed;
        document.getElementById("engine-tab").onclick = loadEngine;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $("#track-tab").on("shown.bs.tab", loadMap)

        $("#log-tab").on("shown.bs.tab", loadLog)

        $("#data-tab").on("shown.bs.tab", loadTable)

        $("#graph-tab").on("shown.bs.tab", loadChart)

        $(document).on("click", ".viewTrack", function() {
            $('#boat_details').show();
            $("#map").removeClass("d-none")

            $("#track-tab-pane").removeClass('active').removeClass('show')
            $("#track-tab").removeClass('active')

            $("#myTab").removeClass('d-none')
			location.hash = '#map';


            let date = $(this).closest('tr').find('td[data-date]').data('date');
            let mac = $(this).closest('tr').find('small[data-mac]').data('mac');

            $("#hidden-date").val(date)
            $("#hidden-mac").val(mac)

            $("body").attr("map-loading", true)
            $("body").attr("data-loading", true)


            $("#track-tab").click()
			
			
        });
        $(document).on("click", ".viewData", function() {
            $('#boat_details').show();
            $("#map").removeClass("d-none")

            $("#data-tab-pane").removeClass('active').removeClass('show')
            $("#data-tab").removeClass('active')

            $("#myTab").removeClass('d-none')

            let date = $(this).closest('tr').find('td[data-date]').data('date');
            let mac = $(this).closest('tr').find('small[data-mac]').data('mac');

            $("#hidden-date").val(date)
            $("#hidden-mac").val(mac)

            $("body").attr("data-loading", true)
            $("body").attr("map-loading", true)

            $("#data-tab").click()
        });
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
	                osmAttrib = 'Map data © <a href="http://openstreetmap.org">OpenSeaMap</a> contributors';
                    osm = new L.TileLayer(osmUrl, {
                        minZoom: 4,
                        maxZoom: 20,
                        attribution: osmAttrib
                    });
                    
                    oSeam = new L.TileLayer(oSeamUrl, {minZoom: 4, maxZoom: 20});	
                    
                    
                    map.addLayer(osm);
                    map.addLayer(oSeam);
                    
                    
                    console.log('first')
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
                            iconUrl: '/assets/img/map/rpm.png'
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
                        }).addTo(map).bindPopup("Max Speed: <b>" + data.maxes.speed.sog + " kts</b><br>Time: <b>" + data.maxes.speed.utc + "</b>");
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
                    mac: mac,
                    front: 1
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
                            { data: 'awa' }
                        ]
                    });
                    
                    $('#data').addClass('table-responsive');
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
                //console.log(data.myRPM1)
				//console.log('myCOOLT1:', data.myCOOLT1);
				console.log('myRPM1 sample:', data.myRPM1.slice(0, 5));
    console.log('mySOG sample:', data.mySOG.slice(0, 5));
    console.log('myLOAD1 sample:', data.myLOAD1.slice(0, 5));
				
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
                            label: { text: 'RPM', minValue: 0, maxValue: 4000 }
                        },
                        scaleY2: {
                            label: { text: 'Boat Speed (kts)', visible: false }
                        },
                        scaleY3: {
                            label: { text: 'Boost' },
							   },
                        scaleY4: {
                            label: { text: 'Fuel Rate', visible: false }
                        },
                        scaleY5: {
                            label: { text: 'Litres per NM', visible: false }
                        },
						scaleY6: {
                            label: { text: 'Temp', minValue: 60, maxValue: 100,visible: false }
                        },
                        series: [
                            {   
                                scales: "scaleX,scaleY",
                                values: data.myRPM1, 
                                decimals: 0,
                                text: 'RPM',     
                            },   
							{        
                                scales: "scaleX,scaleY2",
                                values: data.mySOG,  
                                decimals: 1,
                                visible: true,
                                text: 'SOG'     
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
                                scales: "scaleX,scaleY5",
                                values: data.myECON1,   
                                decimals: 1,
								visible: false,
                                text: 'SOG',     
                            }, 
                            {       
                                scales: "scaleX,scaleY6",
                                values: data.myCOOLT1,   
                                decimals: 0,
                                text: 'Coolant Temp',     
                            }, 
                            
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

    })
</script>

@endsection