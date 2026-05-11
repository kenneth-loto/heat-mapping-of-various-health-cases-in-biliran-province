<?php
include 'header.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>New Resident</title>
    <!-- Include Leaflet CSS and JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-75 shadow-lg">
        <div class="card-body">
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">New Resident</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;">
                <form method="post" action="../navigate.php" enctype="multipart/form-data">
                    <div class="row text-dark">

                        <!-- Resident ID -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="resident_id">Resident ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="resident_id" placeholder="Enter 11-character Resident ID" required>
                        </div>

                        <!-- First Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter first name" required>
                        </div>

                        <!-- Middle Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="middle_name">Middle Name (Optional)</label>
                            <input type="text" class="form-control" name="middle_name" placeholder="Enter middle name">
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter last name" required>
                        </div>

                        <!-- Suffix -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="suffix">Suffix (Optional)</label>
                            <select class="form-select" name="suffix">
                                <option value="" selected>None</option>
                                <option value="Sr">Sr</option>
                                <option value="Jr">Jr</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>

                        <!-- Municipal -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="municipal">Municipal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="municipal" placeholder="Enter municipal" required>
                        </div>

                        <!-- Barangay -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="barangay">Barangay <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="barangay" placeholder="Enter barangay" required>
                        </div>

                        <!-- Street -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="street">Street (Optional)</label>
                            <input type="text" class="form-control" name="street" placeholder="Enter street">
                        </div>

                        <!-- Birth Date -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="birth_date">Birth Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="birth_date" required>
                        </div>

                        <!-- Sex -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="sex">Sex <span class="text-danger">*</span></label>
                            <select class="form-select" name="sex" required>
                                <option value="" disabled selected>Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <!-- Latitude -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="latitude">Latitude (Optional)</label>
                            <input type="number" step="any" class="form-control" id="latitude" name="latitude" placeholder="Enter latitude or select on the map">
                        </div>

                        <!-- Longitude -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="longitude">Longitude (Optional)</label>
                            <input type="number" step="any" class="form-control" id="longitude" name="longitude" placeholder="Enter longitude or select on the map">
                        </div>

                        <!-- Map Container -->
                        <div id="map" style="height: 400px;"></div>

                        <script>
                            // Initialize the map and set its view to a default location
                            var map = L.map('map').setView([12.8797, 121.7740], 5); // Example: Philippines center

                            // Add OpenStreetMap tiles
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);

                            var marker;

                            // Function to update latitude and longitude input fields
                            function updateLatLng(lat, lng) {
                                document.getElementById('latitude').value = lat;
                                document.getElementById('longitude').value = lng;
                            }

                            // Event listener for map clicks
                            map.on('click', function(e) {
                                var lat = e.latlng.lat;
                                var lng = e.latlng.lng;

                                // If marker exists, update its position
                                if (marker) {
                                    marker.setLatLng(e.latlng);
                                } else {
                                    // Create new marker
                                    marker = L.marker(e.latlng, { draggable: true }).addTo(map);

                                    // Update input fields when marker is dragged
                                    marker.on('dragend', function(event) {
                                        var position = event.target.getLatLng();
                                        updateLatLng(position.lat, position.lng);
                                    });
                                }

                                // Update inputs with clicked location
                                updateLatLng(lat, lng);
                            });
                        </script>

                    </div>

                    <!-- Centered Buttons -->
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-add-resident" onclick="return confirm('Are you sure you want to submit? Please review the details before proceeding.');" title="Submit">Submit</button>
                        <a href="residents.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" title="Cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
