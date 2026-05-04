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
        // Check In / Check Out Tab Toggle
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

    async function forceLocationOnLoad() {
        const statusDiv = document.createElement('div');
        statusDiv.id = 'location-status';
        statusDiv.style.cssText = 'padding:12px; margin:10px 0; border-radius:6px; font-weight:bold; text-align:center;';
        document.querySelector('.card-body').prepend(statusDiv);

        function showStatus(msg, color = '#856404') {
            statusDiv.innerHTML = msg;
            statusDiv.style.color = color;
            statusDiv.style.backgroundColor = color + '22';
        }

        showStatus('📍 GPS চালু করা হচ্ছে... অনুগ্রহ করে অপেক্ষা করুন', '#17a2b8');

        try {
            // ==================== MEDIAN BRIDGE ====================
            if (typeof median !== "undefined") {
                console.log("✅ Median Detected");

                // Android-এ GPS Services অন করার জন্য (সবচেয়ে গুরুত্বপূর্ণ)
                if (median.android && median.android.geoLocation) {
                    console.log("Prompting Location Services...");
                    try {
                        await median.android.geoLocation.promptLocationServices();
                    } catch (e) {
                        console.log("promptLocationServices error:", e);
                    }
                }
            }

            // ==================== ACTUAL LOCATION ====================
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    const acc = Math.round(position.coords.accuracy);

                    console.log(`✅ SUCCESS → Lat: ${lat}, Lng: ${lng}, Accuracy: ${acc}m`);

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('latitude2').value = lat;
                    document.getElementById('longitude2').value = lng;

                    initMap('map-checkin', lat, lng);
                    initMap('map-checkout', lat, lng);

                    showStatus(`✅ লোকেশন সফল! Accuracy: ${acc}m`, '#28a745');
                },
                function(error) {
                    console.error("Geolocation Error Code:", error.code);
                    let msg = "❌ লোকেশন পাওয়া যাচ্ছে না।<br>GPS চালু আছে কিনা চেক করুন।";
                    showStatus(msg, '#dc3545');
                }, {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                }
            );

        } catch (e) {
            console.error("Catch Error:", e);
            showStatus('❌ কিছু সমস্যা হয়েছে। পেজ রিফ্রেশ করুন।', '#dc3545');
        }
    }

    // Leaflet Map Function
    function initMap(mapId, lat, lng) {
        const container = document.getElementById(mapId);
        if (!container) return;

        // Clear previous map
        container.innerHTML = '';

        const map = L.map(mapId).setView([lat, lng], 19);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('আপনার বর্তমান অবস্থান')
            .openPopup();
    }

    // Page Load
    window.addEventListener('load', function() {
        // Date & Time
        const now = new Date();
        const currentDate = now.toISOString().slice(0, 10);
        const currentTime = now.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit'
        });

        document.getElementById('current-date').value = currentDate;
        document.getElementById('current-date2').value = currentDate;
        document.getElementById('current-time').value = currentTime;
        document.getElementById('current-time2').value = currentTime;

        // Force Location
        setTimeout(() => {
            forceLocationOnLoad();
        }, 800);
    });
</script>
@endsection

<!--@section('scripts')-->
<!--    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>-->
<!--    <script>-->
<!--        $(document).ready(function() {-->
<!--            if ("{{ session()->get('sign') }}" == "0") {-->
<!--                $('#check_in').addClass('active show');-->
<!--                $('#check_out').removeClass('active show');-->
<!--                $('.check_out').css('background', '#8fbc8f');-->
<!--                $('.check_in').css('background', 'green');-->
<!--            } else if ("{{ session()->get('sign') }}" == "1") {-->
<!--                $('#check_out').addClass('active show');-->
<!--                $('#check_in').removeClass('active show');-->
<!--                $('.check_in').css('background', '#8fbc8f');-->
<!--                $('.check_out').css('background', 'green');-->
<!--            } else {-->
<!--                $('.check_out').css('background', '#8fbc8f');-->
<!--                $('.check_in').css('background', 'green');-->
<!--            }-->

<!--            $('.check_in').on('click', function() {-->
<!--                $('#check_in').addClass('active show');-->
<!--                $('#check_out').removeClass('active show');-->
<!--                $('.check_out').css('background', '#8fbc8f');-->
<!--                $('.check_in').css('background', 'green');-->

<!--            })-->
<!--            $('.check_out').on('click', function() {-->
<!--                $('#check_out').addClass('active show');-->
<!--                $('#check_in').removeClass('active show');-->
<!--                $('.check_in').css('background', '#8fbc8f');-->
<!--                $('.check_out').css('background', 'green');-->

<!--            })-->
<!--        })-->
<!--    </script>-->

<!--    <script>-->
<!--        function initMap(id, lat, lng) {-->
// Initialize the map and set its view to the user's location
<!--            var map = L.map(id).setView([lat, lng], 18);-->

// Load and display the tile layer from OpenStreetMap
<!--            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {-->
<!--                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'-->
<!--            }).addTo(map);-->

// Add a marker at the user's location
<!--            L.marker([lat, lng]).addTo(map)-->
<!--                .bindPopup('You are here!')-->
<!--                .openPopup();-->
<!--        }-->

// Function to get the user's location
<!--        function getLocation() {-->
<!--            if (navigator.geolocation) {-->
<!--                navigator.geolocation.getCurrentPosition(function(position) {-->
<!--                    var lat = position.coords.latitude;-->
<!--                    var lng = position.coords.longitude;-->

// Set the hidden input values for both maps
<!--                    document.getElementById('latitude').value = lat;-->
<!--                    document.getElementById('longitude').value = lng;-->
<!--                    document.getElementById('latitude2').value = lat;-->
<!--                    document.getElementById('longitude2').value = lng;-->

// Initialize both maps with the user's location
initMap('map-checkin', lat, lng); // Map for Check In
initMap('map-checkout', lat, lng); // Map for Check Out
<!--                }, function() {-->
<!--                    alert('Geolocation failed or permission denied.');-->
<!--                });-->
<!--            } else {-->
<!--                alert('Geolocation is not supported by this browser.');-->
<!--            }-->
<!--        }-->

<!--        window.onload = function() {-->
// Get current date and time
<!--            var currentDate = new Date().toISOString().slice(0, 10);-->
<!--            var currentTime = new Date().toLocaleTimeString('en-GB', {-->
<!--                hour: '2-digit',-->
<!--                minute: '2-digit'-->
<!--            });-->

// Set date and time values
<!--            document.getElementById('current-date2').value = currentDate;-->
<!--            document.getElementById('current-date').value = currentDate;-->
<!--            document.getElementById('current-time').value = currentTime;-->
<!--            document.getElementById('current-time2').value = currentTime;-->

// Get user's location and display maps
<!--            getLocation();-->
<!--        }-->
<!--    </script>-->


<!--@endsection-->

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
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    @if (helper::roleAccess('hrm.attendance.index'))
                    <li class="breadcrumb-item"><a href="{{ route('hrm.attendance.index') }}">Attendence List</a></li>
                    @endif
                    <li class="breadcrumb-item active"><span>Add New Attendence</span></li>
                </ol>
            </div>
        </div>
    </div>
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

    .btn-submit-attendance {
        transition: opacity 0.3s, cursor 0.3s;
    }

    .btn-submit-attendance:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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
                    <a class="btn btn-default" href="{{ route('hrm.attendance.create') }}">
                        <i class="fa fa-plus"></i> Add New
                    </a>
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

            <div class="card-body">
                <button class="check_in btn-custom" type="button">Check In</button>
                <button class="check_out btn-custom" type="button">Check Out</button>

                {{-- ─── CHECK IN ─── --}}
                <div class="collapse active show" id="check_in">
                    <div class="card-header">
                        <h4 class="card-title">Check In</h4>
                    </div>
                    <div class="card card-body">
                        <form class="needs-validation" method="POST" action="{{ route('hrm.attendance.sign_in') }}" novalidate>
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
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="intime" class="col-sm-3 col-form-label">Date *</label>
                                <div class="col-md-4 mb-1">
                                    <input type="date" id="current-date" class="form-control" name="date" readonly>
                                    @error('date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="Emplyee">Punch Time* <span class="text-danger">*</span></label>
                                <div class="col-md-4 mb-1">
                                    <input type="time" id="current-time" class="form-control" name="sign_in" readonly>
                                    @error('sign_in')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Location</label>
                                <div class="col-md-4 mb-1">
                                    <div id="gps-status-checkin" class="gps-status-box mb-2"
                                        style="padding:10px; border-radius:6px; font-weight:bold; text-align:center; background:#fff3cd; color:#856404;">
                                        📍 GPS লোড হচ্ছে...
                                    </div>
                                    <div id="map-checkin" style="height: 200px; width: 100%; display:none;"></div>
                                    <input type="hidden" id="latitude" name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">
                                </div>
                            </div>
                            <div class="form-group row">
                                <button class="btn btn-info btn-submit-attendance" id="submit-checkin" type="submit" disabled>
                                    <i class="fa fa-save"></i> &nbsp;Save
                                </button>
                                &nbsp;
                                <button type="button" class="btn btn-warning" id="reload-checkin">
                                    <i class="fa fa-refresh"></i> &nbsp;Reload Location
                                </button>
                            </div>



                        </form>
                    </div>
                </div>

                {{-- ─── CHECK OUT ─── --}}
                <div class="collapse" id="check_out">
                    <div class="card-header">
                        <h4 class="card-title">Check Out</h4>
                    </div>
                    <div class="card card-body">
                        <form class="needs-validation" method="POST" action="{{ route('hrm.attendance.sign_out') }}" novalidate>
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
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="intime" class="col-sm-3 col-form-label">Date *</label>
                                <div class="col-md-4 mb-1">
                                    <input type="date" id="current-date2" class="form-control" name="date" readonly>
                                    @error('date')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="Emplyee">Punch Time* <span class="text-danger">*</span></label>
                                <div class="col-md-4 mb-1">
                                    <input type="time" id="current-time2" class="form-control" name="sign_out" readonly>
                                    @error('sign_out')
                                    <span class="error text-red text-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Location</label>
                                <div class="col-md-4 mb-1">
                                    <div id="gps-status-checkout" class="gps-status-box mb-2"
                                        style="padding:10px; border-radius:6px; font-weight:bold; text-align:center; background:#fff3cd; color:#856404;">
                                        📍 GPS লোড হচ্ছে...
                                    </div>
                                    <div id="map-checkout" style="height: 200px; width: 100%; display:none;"></div>
                                    <input type="hidden" id="latitude2" name="latitude">
                                    <input type="hidden" id="longitude2" name="longitude">
                                </div>
                            </div>

                            <div class="form-group row">
                                <button class="btn btn-info btn-submit-attendance" id="submit-checkout" type="submit" disabled>
                                    <i class="fa fa-save"></i> &nbsp;Save
                                </button>
                                &nbsp;
                                <button type="button" class="btn btn-warning" id="reload-checkout">
                                    <i class="fa fa-refresh"></i> &nbsp;Reload Location
                                </button>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--    <button class="btn btn-info btn-submit-attendance" id="submit-checkout" type="submit" disabled>-->
                            <!--        <i class="fa fa-save"></i> &nbsp;Save-->
                            <!--    </button>-->
                            <!--</div>-->
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
        // ─── Tab Toggle ───────────────────────────────────────────
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

    // ─── GPS Status Helper ─────────────────────────────────────────
    function setGpsStatus(boxId, msg, type) {
        const el = document.getElementById(boxId);
        if (!el) return;

        const styles = {
            loading: {
                bg: '#fff3cd',
                color: '#856404'
            },
            success: {
                bg: '#d4edda',
                color: '#155724'
            },
            error: {
                bg: '#f8d7da',
                color: '#721c24'
            },
        };
        const s = styles[type] || styles.loading;
        el.style.background = s.bg;
        el.style.color = s.color;
        el.innerHTML = msg;
    }

    // ─── Leaflet Map ───────────────────────────────────────────────
    var mapCheckin = null;
    var mapCheckout = null;

    function initMap(mapId, lat, lng) {
        const container = document.getElementById(mapId);
        if (!container) return;

        container.style.display = 'block';
        container.innerHTML = '';

        if (mapId === 'map-checkin' && mapCheckin) {
            mapCheckin.remove();
            mapCheckin = null;
        }
        if (mapId === 'map-checkout' && mapCheckout) {
            mapCheckout.remove();
            mapCheckout = null;
        }

        const map = L.map(mapId).setView([lat, lng], 17);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('আপনার বর্তমান অবস্থান')
            .openPopup();

        if (mapId === 'map-checkin') mapCheckin = map;
        if (mapId === 'map-checkout') mapCheckout = map;
    }

    // ─── GPS → Map → Submit enable ────────────────────────────────
    async function forceLocationOnLoad() {

        document.getElementById('submit-checkin').disabled = true;
        document.getElementById('submit-checkout').disabled = true;

        setGpsStatus('gps-status-checkin', '📍 GPS চালু করা হচ্ছে... অনুগ্রহ করে অপেক্ষা করুন', 'loading');
        setGpsStatus('gps-status-checkout', '📍 GPS চালু করা হচ্ছে... অনুগ্রহ করে অপেক্ষা করুন', 'loading');

        try {
            if (typeof median !== 'undefined') {
                if (median.android && median.android.geoLocation) {
                    try {
                        await median.android.geoLocation.promptLocationServices();
                    } catch (e) {}
                }
            }

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    const acc = Math.round(position.coords.accuracy);

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                    document.getElementById('latitude2').value = lat;
                    document.getElementById('longitude2').value = lng;

                    try {
                        initMap('map-checkin', lat, lng);
                    } catch (e) {
                        console.error('map-checkin error:', e);
                    }
                    try {
                        initMap('map-checkout', lat, lng);
                    } catch (e) {
                        console.error('map-checkout error:', e);
                    }

                    const successMsg = `✅ লোকেশন পাওয়া গেছে। নির্ভুলতা: ${acc}m`;
                    setGpsStatus('gps-status-checkin', successMsg, 'success');
                    setGpsStatus('gps-status-checkout', successMsg, 'success');

                    document.getElementById('submit-checkin').disabled = false;
                    document.getElementById('submit-checkout').disabled = false;
                },

                function(error) {
                    let msg = '';
                    switch (error.code) {
                        case 1:
                            msg = '❌ লোকেশন অনুমতি দেওয়া হয়নি। ব্রাউজার/অ্যাপ সেটিংস থেকে অনুমতি দিন এবং পেজ রিফ্রেশ করুন।';
                            break;
                        case 2:
                            msg = '❌ GPS সিগন্যাল পাওয়া যাচ্ছে না। বাইরে যান বা GPS চালু আছে কিনা দেখুন।';
                            break;
                        case 3:
                            msg = '❌ GPS timeout হয়েছে। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।';
                            break;
                        default:
                            msg = '❌ অজানা GPS সমস্যা। পেজ রিফ্রেশ করুন।';
                    }
                    setGpsStatus('gps-status-checkin', msg, 'error');
                    setGpsStatus('gps-status-checkout', msg, 'error');

                    document.getElementById('submit-checkin').disabled = true;
                    document.getElementById('submit-checkout').disabled = true;
                },

                {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                }
            );

        } catch (e) {
            console.error('forceLocationOnLoad error:', e);
            const errMsg = '❌ কিছু সমস্যা হয়েছে। পেজ রিফ্রেশ করুন।';
            setGpsStatus('gps-status-checkin', errMsg, 'error');
            setGpsStatus('gps-status-checkout', errMsg, 'error');
        }
    }

    // ─── Reload Location Button ────────────────────────────────
    document.getElementById('reload-checkin').addEventListener('click', function() {
        location.reload();
    });

    document.getElementById('reload-checkout').addEventListener('click', function() {
        location.reload();
    });

    // ─── Page Load ────────────────────────────────────────────────
    window.addEventListener('load', function() {
        const now = new Date();
        const currentDate = now.toISOString().slice(0, 10);
        const currentTime = now.toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit'
        });

        document.getElementById('current-date').value = currentDate;
        document.getElementById('current-date2').value = currentDate;
        document.getElementById('current-time').value = currentTime;
        document.getElementById('current-time2').value = currentTime;

        setTimeout(forceLocationOnLoad, 800);
    });

    // ─── Submit Button Click Guard ─────────────────────────────────
    document.getElementById('submit-checkin').addEventListener('click', function(e) {
        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;
        const mapVisible = document.getElementById('map-checkin').style.display !== 'none';

        if (!lat || !lng || !mapVisible) {
            e.preventDefault();

            alert('📍 লোকেশন এখনো নির্ধারণ হয়নি।\nঅনুগ্রহ করে GPS চালু করুন।\nপেজ রিফ্রেশ হচ্ছে...');

            setTimeout(function() {
                location.reload();
            }, 2000);
        }
    });

    document.getElementById('submit-checkout').addEventListener('click', function(e) {
        const lat = document.getElementById('latitude2').value;
        const lng = document.getElementById('longitude2').value;
        const mapVisible = document.getElementById('map-checkout').style.display !== 'none';

        if (!lat || !lng || !mapVisible) {
            e.preventDefault();

            alert('📍 লোকেশন এখনো নির্ধারণ হয়নি।\nঅনুগ্রহ করে GPS চালু করুন।\nপেজ রিফ্রেশ হচ্ছে...');

            setTimeout(function() {
                location.reload();
            }, 2000);
        }
    });
</script>
@endsection