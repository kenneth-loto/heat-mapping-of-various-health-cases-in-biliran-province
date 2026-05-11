<?php
include 'header.php';

// Get municipality ID from the query string
$id = $_GET['id'];

// Fetch the municipality data (assuming getAMunicipality returns an associative array)
$municipality = $function->getAMunicipality($id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Municipality</title>
    <!-- Include Leaflet CSS and JS -->
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
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">Edit Municipality</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3 mb-4" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;">
                <form method="post" action="../navigate.php" enctype="multipart/form-data">
                    <div class="row text-dark">

                        <!-- Hidden ID Field -->
                        <input type="hidden" name="id" value="<?= $municipality->id; ?>" />

                        <!-- Municipality Name -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="municipality_name">Municipality <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="municipality_name" value="<?= isset($_SESSION['form_data']['municipality_name']) ? htmlspecialchars($_SESSION['form_data']['municipality_name']) : htmlspecialchars($municipality->municipality_name); ?>" placeholder="e.g. Naval" required>
                        </div>

                        <!-- Municipality Code -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="municipal_code">Municipality Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="municipal_code" value="<?= isset($_SESSION['form_data']['municipal_code']) ? htmlspecialchars($_SESSION['form_data']['municipal_code']) : htmlspecialchars($municipality->municipal_code); ?>" placeholder="e.g. 087801000" required>
                        </div>

                        <!-- No of Population -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="no_of_population">No of Population <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_of_population" value="<?= isset($_SESSION['form_data']['no_of_population']) ? htmlspecialchars($_SESSION['form_data']['no_of_population']) : htmlspecialchars($municipality->no_of_population); ?>" 
                            placeholder="e.g. 20000" required
                            pattern="^[1-9][0-9]*$" 
                            title="Please enter a valid number (no decimal, positive integer)">
                        </div>

                        <!-- Geometry -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="geometry">Geometry <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="geometry" name="geometry" value="<?= htmlspecialchars($municipality->geom); ?>" placeholder="Draw on the map" readonly required hidden>
                            <small class="form-text text-muted">
                                Please use the draw a polygon or draw a rectangle tool on the map to define the boundaries of the municipality.
                            </small>
                        </div>

                        <!-- Map Integration -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map" style="height: 400px;"></div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-update-municipality" onclick="return confirm('Are you sure you want to submit? Please review your details before proceeding.');" style="justify-content: center !important;" title="Submit">Submit</button>
                            <a href="municipalities.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" style="justify-content: center !important;" title="Cancel">Cancel</a>
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
    <?php if (!empty($municipality->geom)) { ?>
        var existingGeom = JSON.parse('<?= addslashes($municipality->geom); ?>');

        // Coordinates provided (Make sure they follow the [lng, lat] order for Leaflet)
        existingGeom.forEach(function(geometry) {
            var latlngs = geometry.map(function(coord) {
                return [coord[1], coord[0]]; // Leaflet uses [lat, lng], not [lng, lat]
            });

            // Create a polygon with the existing coordinates and add it to the map
            var polygon = L.polygon(latlngs, { color: 'blue', weight: 2 }).addTo(map);

            // Optionally, zoom to the bounds of the polygon
            map.fitBounds(polygon.getBounds());

            // Add the existing polygon to the drawnItems feature group
            drawnItems.addLayer(polygon);
        });
    <?php } ?>

    // Event listener for when a polygon is created
    map.on('draw:created', function(e) {
        var layer = e.layer;
        drawnItems.clearLayers(); // Clear existing layers and add the new one
        drawnItems.addLayer(layer);

        // Extract coordinates for multi-polygon support
        var coordinates = layer.getLatLngs().map(function(latlngs) {
            return latlngs.map(function(latlng) {
                return [latlng.lng, latlng.lat]; // Convert to [lng, lat] format
            });
        });

        // Ensure it is wrapped in an additional array to match the required format
        var geometryData = JSON.stringify(coordinates);

        // Populate the hidden input field with the geometry data
        document.getElementById('geometry').value = geometryData;
    });

    // Event listener for when a polygon is deleted
    map.on('draw:deleted', function(e) {
        // Clear the geometry field when a polygon is deleted
        document.getElementById('geometry').value = '';
    });
</script>

</body>
</html>
