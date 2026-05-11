<?php
include 'header.php';

// Get barangay ID from the query string
$id = $_GET['id'];

// Fetch the barangay data
$barangay = $function->getABarangay($id);

// Fetch all municipalities for the dropdown
$municipalities = $function->getAllMunicipalities();
?>

<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Include Leaflet Draw CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <!-- Include Font Awesome for the icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card w-50 shadow-lg">
        <div class="card-body">
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">Edit Barangay</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3 mb-4" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;">
                <form method="post" action="../navigate.php" enctype="multipart/form-data" id="editBarangayForm">
                    <div class="row text-dark">
                        <input type="hidden" name="id" value="<?= $barangay->id; ?>" />

                        <!-- Municipality Select -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="municipality_name">Municipality <span class="text-danger">*</span></label>
                            <select class="form-select" id="municipality_name" name="municipality_name" required>
                                <option value="">Select Municipality</option>
                                <?php
                                // Loop through the municipalities array to create options
                                foreach ($municipalities as $municipality) {
                                    $selected = ($barangay->municipality_name == $municipality['municipality_name']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($municipality['municipality_name']) . '" data-geometry=\'' . json_encode($municipality['geom']) . '\' ' . $selected . '>' . htmlspecialchars($municipality['municipality_name']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Barangay Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="barangay_code">Barangay Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="barangay_code" value="<?= isset($_SESSION['form_data']['barangay_code']) ? htmlspecialchars($_SESSION['form_data']['barangay_code']) : htmlspecialchars($barangay->barangay_code); ?>" placeholder="e.g. San Juan" required>
                        </div>

                        <!-- Barangay Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="barangay_name">Barangay <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="barangay_name" value="<?= isset($_SESSION['form_data']['barangay_name']) ? htmlspecialchars($_SESSION['form_data']['barangay_name']) : htmlspecialchars($barangay->barangay_name); ?>" placeholder="e.g. San Juan" required>
                        </div>

                        <!-- No. of Population -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="no_of_population">No. of Population <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="no_of_population" value="<?= isset($_SESSION['form_data']['no_of_population']) ? htmlspecialchars($_SESSION['form_data']['no_of_population']) : htmlspecialchars($barangay->no_of_population); ?>" placeholder="e.g. 1000" required>
                        </div>

                        <!-- Geometry -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="geometry">Geometry <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="geometry" name="geometry" value="<?= htmlspecialchars($barangay->geom); ?>" placeholder="Draw on the map" readonly required hidden>
                            <small class="form-text text-muted">
                                Please use the draw polygon or draw rectangle tool on the map to define the boundary of the barangay, ensuring it is within the boundary of the municipality.
                            </small>
                        </div>

                        <!-- Map Integration -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map" style="height: 400px;"></div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-center align-items-center mt-5">
                            <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-update-barangay" onclick="return confirm('Are you sure you want to submit? Please review your details before proceeding.');" title="Submit" style="justify-content: center !important;">Submit</button>
                            <a href="barangays.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" title="Cancel" style="justify-content: center !important;">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize the map
    var map = L.map('map').setView([11.643319214056731, 124.47612493038605], 10); // Centered on the Philippines

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Initialize the feature group for drawn items
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Initialize the draw control and pass it the FeatureGroup of editable layers
    var drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems
        },
        draw: {
            polygon: true,
            polyline: false,
            rectangle: true,
            circle: false,
            marker: false
        }
    });

    // Add the draw control
    map.addControl(drawControl); 

    // Check if there is existing geometry (coordinates) from the database
    <?php if (!empty($barangay->geom)) { ?>
        var existingGeom = JSON.parse('<?= addslashes($barangay->geom); ?>');

        // Coordinates provided (Make sure they follow the [lng, lat] order for Leaflet)
        var latlngs = existingGeom[0].map(function(coord) {
            return [coord[1], coord[0]]; // Leaflet uses [lat, lng], not [lng, lat]
        });

        // Create a polygon with the existing coordinates and add it to the map
        var polygon = L.polygon(latlngs, { color: 'blue', weight: 2 }).addTo(map);

        // Optionally, zoom to the bounds of the polygon
        map.fitBounds(polygon.getBounds());

        // Add the existing polygon to the drawnItems feature group
        drawnItems.addLayer(polygon);
    <?php } ?>

    // Event listener for when a municipality is selected from the dropdown
    document.getElementById('municipality_name').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var geometryData = selectedOption.getAttribute('data-geometry');

        if (geometryData) {
            // Parse the geometry data
            var geometry = JSON.parse(geometryData);

            // If geometry exists, zoom to it
            var latlngs = geometry[0].map(function(coord) {
                return [coord[1], coord[0]]; // Convert [lng, lat] to [lat, lng] for Leaflet
            });

            // Create a polygon for the selected municipality
            var polygon = L.polygon(latlngs, { color: 'blue', weight: 2 }).addTo(map);

            // Zoom to the bounds of the polygon
            map.fitBounds(polygon.getBounds());

            // Clear any existing drawn items and add the new polygon
            drawnItems.clearLayers();
            drawnItems.addLayer(polygon);

            // Update the geometry input field with the new geometry data
            document.getElementById('geometry').value = JSON.stringify([latlngs]);
        }
    });
</script>

</body>
</html>
