<?php
include 'header.php';

// Assuming you have already instantiated your class that includes getAllMunicipalities function.
$municipalities = $function->getAllMunicipalities();

$email = $_SESSION['email'];
$municipalityFromSession = $function->getMunicipalityByEmail($email);

if (!$municipality && $municipalityFromSession) {
    $municipality = $municipalityFromSession;
}

?>

<html>
<head>
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
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">New Barangay</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;"> 

            <form method="post" action="../navigate.php">

                <!-- Municipality Dropdown -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="municipality_name">Municipality <span class="text-danger">*</span></label>
                        <select class="form-select" id="municipality_name" name="municipality_name" style="pointer-events: none;" required>
                            <option value="">Select Municipality</option>
                            <?php
                            // Loop through the municipalities array to create options with the municipality_name as value
                            foreach ($municipalities as $municipality) {
                                // Check if this municipality matches the one from the session
                                $selected = ($municipality['municipality_name'] == $municipalityFromSession) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($municipality['municipality_name']) . '" ' . $selected . ' data-geometry=\'' . json_encode($municipality['geom']) . '\'>' . htmlspecialchars($municipality['municipality_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="barangay_code">Barangay Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="barangay_code" value="<?php echo isset($_SESSION['form_data']['barangay_code']) ? htmlspecialchars($_SESSION['form_data']['barangay_code']) : ''; ?>" placeholder="e.g. 087802005" required>
                    </div>
                </div>

                <!-- Barangay Name Input -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="barangay_name">Barangay <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="barangay_name" value="<?php echo isset($_SESSION['form_data']['barangay_name']) ? htmlspecialchars($_SESSION['form_data']['barangay_name']) : ''; ?>" placeholder="e.g. San Juan" required>
                    </div>
                </div>

                <!-- Population Input -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="no_of_population">No. of Population <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_of_population" 
                            value="<?php echo isset($_SESSION['form_data']['no_of_population']) ? htmlspecialchars($_SESSION['form_data']['no_of_population']) : ''; ?>"
                            placeholder="e.g. 20000" required
                            pattern="^[1-9][0-9]*$" 
                            title="Please enter a valid number (no decimal, positive integer)">
                    </div>
                </div>

                <!-- Map Integration -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="geometry">Geometry <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="geometry" name="geometry" placeholder="Draw on the map" readonly required hidden>
                        <small class="form-text text-muted">
                        <small class="form-text text-muted">
                            Please use the draw polygon or draw rectangle tool on the map to define the boundary of the barangay, ensure it is within the boundary of the municipality.
                        </small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-municipality-user-add-barangay" onclick="return confirm('Are you sure you want to submit? Please review the details before proceeding.');" style="justify-content: center !important;" title="Submit">Submit</button>
                    <a href="barangays.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" style="justify-content: center !important;" title="Cancel">Cancel</a>
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

    // Event listener for when a polygon is created
    map.on('draw:created', function(e) {
        var layer = e.layer;
        drawnItems.addLayer(layer);

        // Extract only coordinates
        var coordinates = layer.getLatLngs()[0].map(function(latlng) {
            return [latlng.lng, latlng.lat];
        });

        // Ensure it is wrapped in an additional array to match the required format
        var geometryData = JSON.stringify([coordinates]);

        // Populate the hidden input field with the simplified geometry data
        document.getElementById('geometry').value = geometryData;
    });

    // Event listener for when a polygon is deleted
    map.on('draw:deleted', function(e) {
        // Clear the geometry field when a polygon is deleted
        document.getElementById('geometry').value = '';
    });

    // Add custom "Reset View" button with updated icon and size to match draw icons
    var resetViewButton = L.Control.extend({
        options: {
            position: 'topleft'
        },
        onAdd: function(map) {
            var button = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
            button.innerHTML = '<i class="fas fa-sync-alt"></i>'; // Updated reset icon (sync)
            button.style.backgroundColor = 'white';
            button.style.border = '1px solid #ccc';
            button.style.padding = '6px'; // Adjusted padding to match size of draw icons
            button.style.cursor = 'pointer';
            button.style.width = '33px'; // Set fixed width to match draw icon size
            button.style.height = '30px'; // Set fixed height to match draw icon size

            button.onclick = function(e) {
                // Prevent the form from submitting or triggering any form-related action
                e.stopPropagation();  // Prevent the event from bubbling up
                e.preventDefault();  // Prevent the default action (form submission, etc.)

                // Reset map view to the initial position without affecting form data
                map.setView([11.643319214056731, 124.47612493038605], 10); // Reset to original view
                map.eachLayer(function(layer) {
                    if (layer instanceof L.FeatureGroup) {
                        layer.clearLayers(); // Clear drawn items on reset
                    }
                });
            };

            return button;
        }
    });

    map.addControl(new resetViewButton()); // Add Reset View button to the map

    map.addControl(drawControl); // Add the draw control

    // Update map view based on selected municipality
    document.getElementById('municipality_name').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var geomData = selectedOption.getAttribute('data-geometry');
        if (geomData) {
            var geometry = JSON.parse(geomData);

            // Clear existing layers
            drawnItems.clearLayers();

            // Convert geometry to Leaflet LatLng array and add to map
            var latlngs = geometry[0].map(function(coord) {
                return [coord[1], coord[0]]; // Invert coordinates for Leaflet
            });

            var polygon = L.polygon(latlngs);
            drawnItems.addLayer(polygon);

            // Fit map bounds to the selected municipality
            map.fitBounds(polygon.getBounds());
        }
    });

    // Trigger the 'change' event to update the map when the page loads
    window.onload = function() {
        var municipalityDropdown = document.getElementById('municipality_name');
        if (municipalityDropdown.value) {
            municipalityDropdown.dispatchEvent(new Event('change'));
        }
    };
</script>
</body>
</html>
