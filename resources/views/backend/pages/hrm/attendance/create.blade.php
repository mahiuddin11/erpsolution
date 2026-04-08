@extends('backend.layouts.master')

@section('title')
    Employee - {{ $title }}
@endsection
@section('navbar-content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> HRM </h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                        @if (helper::roleAccess('hrm.attendance.index'))
                            <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Attendence
                                    List</a></li>
                        @endif
                        <li class="breadcrumb-item active"><span>Add New Attendence</span></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

<style>
    .btn-custom {
        padding: 10px 13px;
        border: none;
        border-radius: 5px 5px 0 0;
        background: green;
        color: white
    }
</style>

@section('admin-content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Attendence</h3>
                    <div class="card-tools">
                        @if (helper::roleAccess('hrm.attendance.index'))
                            <a class="btn btn-default" href="{{ route('hrm.attendance.create') }}"><i
                                    class="fa fa-plus"></i>
                                Add New</a>
                        @endif
                        <span id="buttons"></span>
                        <a class="btn btn-tool btn-default" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </a>
                        <a class="btn btn-tool btn-default" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">


                    <button class="check_in btn-custom" type="button">
                        Check In
                    </button>
                    <button class="check_out btn-custom" type="button">
                        Check Out
                    </button>

                    <div class="collapse active show" id="check_in">
                        <div class="card-header">
                            <h4 class="card-title">Check In</h4>
                        </div>
                        <div class="card card-body">
                            <form class="needs-validation" method="POST" action="{{ route('hrm.attendance.sign_in') }}"
                                novalidate>
                                @csrf
                                <div class="form-group row">
                                    <label for="intime" class="col-sm-3 col-form-label">Employee Name*</label>
                                    <div class="col-md-4 mb-1">
                                        @if (auth()->user()->employee)
                                            <input type="hidden" readonly name="emplyee_id"
                                                value="{{ auth()->user()->employee->id ?? 0 }}">
                                        @endif

                                        {{ auth()->user()->employee->name ?? 'No Employee Found' }}
                                        @error('emplyee_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="intime" class="col-sm-3 col-form-label">Date *</label>
                                    <div class="col-md-4 mb-1">
                                        <input type="date" id="current-date" class="form-control" name="date"
                                            readonly>
                                        @error('date')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="Emplyee">Punch Time* <span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-4 mb-1">
                                        <input type="time" id="current-time" class="form-control" name="sign_in"
                                            readonly>
                                        @error('sign_in')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Location</label>
                                    <div class="col-md-4 mb-1">
                                        <div id="map-checkin" style="height: 200px; width: 100%;"></div>
                                        <input type="hidden" id="latitude" name="latitude">
                                        <input type="hidden" id="longitude" name="longitude">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>
                                        &nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="collapse" id="check_out">
                        <div class="card-header">
                            <h4 class="card-title">Check Out</h4>
                        </div>
                        <div class="card card-body">
                            <form class="needs-validation" method="POST" action="{{ route('hrm.attendance.sign_out') }}"
                                novalidate>
                                @csrf
                                <div class="form-group row">
                                    <label for="intime" class="col-sm-3 col-form-label">Employee Name*</label>
                                    <div class="col-md-4 mb-1">
                                        @if (auth()->user()->employee)
                                            <input type="hidden" readonly name="emplyee_id"
                                                value="{{ auth()->user()->employee->id ?? 0 }}">
                                        @endif

                                        {{ auth()->user()->employee->name ?? 'No Employee Found' }}
                                        @error('emplyee_id')
                                            <span class=" error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="intime" class="col-sm-3 col-form-label">Date *</label>
                                    <div class="col-md-4 mb-1">
                                        <input type="date" id="current-date2" class="form-control" name="date"
                                            readonly>
                                        @error('date')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label" for="Emplyee">Punch Time* <span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-4 mb-1">
                                        <input type="time" id="current-time2" class="form-control" name="sign_out"
                                            readonly>
                                        @error('sign_out')
                                            <span class="error text-red text-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Location</label>
                                    <div class="col-md-4 mb-1">
                                        <div id="map-checkout" style="height: 200px; width: 100%;"></div>
                                        <input type="hidden" id="latitude2" name="latitude">
                                        <input type="hidden" id="longitude2" name="longitude">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <button class="btn btn-info" type="submit"><i class="fa fa-save"></i>
                                        &nbsp;Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <!-- /.col-->
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        $(document).ready(function() {
            // Check In / Check Out টগল লজিক (আগেরটা রাখলাম)
            if ("{{ session()->get('sign') }}" == "0") {
                $('#check_in').addClass('active show');
                $('#check_out').removeClass('active show');
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');
            } else if ("{{ session()->get('sign') }}" == "1") {
                $('#check_out').addClass('active show');
                $('#check_in').removeClass('active show');
                $('.check_in').css('background', '#8fbc8f');
                $('.check_out').css('background', 'green');
            } else {
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');
            }

            $('.check_in').on('click', function() {
                $('#check_in').addClass('active show');
                $('#check_out').removeClass('active show');
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');
            });

            $('.check_out').on('click', function() {
                $('#check_out').addClass('active show');
                $('#check_in').removeClass('active show');
                $('.check_in').css('background', '#8fbc8f');
                $('.check_out').css('background', 'green');
            });
        });

        // ==================== Improved Map & Location Logic ====================
        let userLat = null;
        let userLng = null;

        function initMap(mapId, lat, lng) {
            var map = L.map(mapId, {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView([lat, lng], 17);   // 17-18 এর মাঝামাঝি রাখলাম

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup('Your Current Location<br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6))
                .openPopup();
        }

        function getLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by this browser.');
                return;
            }

            // Loading দেখানোর জন্য (ঐচ্ছিক)
            // document.getElementById('map-checkin').innerHTML = '<p style="text-align:center;padding:50px;">Getting your location...</p>';

            navigator.geolocation.getCurrentPosition(
                function(position) {                    // SUCCESS
                    userLat = position.coords.latitude;
                    userLng = position.coords.longitude;

                    console.log("✅ Location Received:", userLat, userLng);
                    console.log("Accuracy:", position.coords.accuracy + " meters");

                    // Hidden inputs এ ভ্যালু সেট
                    document.getElementById('latitude').value = userLat;
                    document.getElementById('longitude').value = userLng;
                    document.getElementById('latitude2').value = userLat;
                    document.getElementById('longitude2').value = userLng;

                    // দুটো ম্যাপই লোড করো
                    initMap('map-checkin', userLat, userLng);
                    initMap('map-checkout', userLat, userLng);
                },
                function(error) {                       // ERROR
                    console.error("Geolocation Error Code:", error.code);
                    let msg = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            msg = "Location permission denied. Please allow it.";
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = "Location information unavailable.";
                            break;
                        case error.TIMEOUT:
                            msg = "Location request timed out.";
                            break;
                        default:
                            msg = "Unknown error getting location.";
                    }
                    alert("Location Error: " + msg);
                },
                {
                    enableHighAccuracy: true,   // ← এটা খুব জরুরি (GPS ব্যবহার করবে)
                    timeout: 15000,             // 15 সেকেন্ড
                    maximumAge: 0               // ক্যাশ ব্যবহার করবে না
                }
            );
        }

        // Page Load হলে
        window.onload = function() {
            // Date & Time সেট
            var currentDate = new Date().toISOString().slice(0, 10);
            var currentTime = new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });

            document.getElementById('current-date').value = currentDate;
            document.getElementById('current-date2').value = currentDate;
            document.getElementById('current-time').value = currentTime;
            document.getElementById('current-time2').value = currentTime;

            // লোকেশন নাও
            getLocation();
        };
    </script>
@endsection

{{-- @section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            if ("{{ session()->get('sign') }}" == "0") {
                $('#check_in').addClass('active show');
                $('#check_out').removeClass('active show');
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');
            } else if ("{{ session()->get('sign') }}" == "1") {
                $('#check_out').addClass('active show');
                $('#check_in').removeClass('active show');
                $('.check_in').css('background', '#8fbc8f');
                $('.check_out').css('background', 'green');
            } else {
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');
            }

            $('.check_in').on('click', function() {
                $('#check_in').addClass('active show');
                $('#check_out').removeClass('active show');
                $('.check_out').css('background', '#8fbc8f');
                $('.check_in').css('background', 'green');

            })
            $('.check_out').on('click', function() {
                $('#check_out').addClass('active show');
                $('#check_in').removeClass('active show');
                $('.check_in').css('background', '#8fbc8f');
                $('.check_out').css('background', 'green');

            })
        })
    </script>

    <script>
        function initMap(id, lat, lng) {
            // Initialize the map and set its view to the user's location
            var map = L.map(id).setView([lat, lng], 18);

            // Load and display the tile layer from OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a marker at the user's location
            L.marker([lat, lng]).addTo(map)
                .bindPopup('You are here!')
                .openPopup();
        }

        // Function to get the user's location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    // Set the hidden input values for both maps
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('latitude2').value = lat;
                    document.getElementById('longitude2').value = lng;

                    // Initialize both maps with the user's location
                    initMap('map-checkin', lat, lng); // Map for Check In
                    initMap('map-checkout', lat, lng); // Map for Check Out
                }, function() {
                    alert('Geolocation failed or permission denied.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        window.onload = function() {
            // Get current date and time
            var currentDate = new Date().toISOString().slice(0, 10);
            var currentTime = new Date().toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit'
            });

            // Set date and time values
            document.getElementById('current-date2').value = currentDate;
            document.getElementById('current-date').value = currentDate;
            document.getElementById('current-time').value = currentTime;
            document.getElementById('current-time2').value = currentTime;

            // Get user's location and display maps
            getLocation();
        }
    </script>
@endsection --}}
