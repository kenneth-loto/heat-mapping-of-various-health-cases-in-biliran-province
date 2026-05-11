<?php
include 'header.php';

// Assuming you have already instantiated your class that includes getMunicipalities and getBarangays functions.
$municipalities = $function->getMunicipalitiesDB(); // Fetch municipalities
$barangays = $function->getBarangaysDB(); // Fetch barangays
$diseases = $function->getNameOfDiseases();
$barangays = $function->getAllBarangaysGeom(); // Fetch barangays with geom data

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
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">New Patient</h3>

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
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="patients_id">Patient's ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="patients_id" value="<?php echo isset($_SESSION['form_data']['patients_id']) ? htmlspecialchars($_SESSION['form_data']['patients_id']) : ''; ?>" placeholder="e.g. 2024-11-16" required>
                    </div>
                </div>

                <!-- Municipality Dropdown -->
                <<!-- Municipality Dropdown -->
				<div class="row text-dark">
				    <div class="col-md-12 mb-4">
				        <label class="form-label" for="municipality_name">Municipality <span class="text-danger">*</span></label>
				        <select class="form-select" id="municipality_name" name="municipality_name" style="pointer-events: none;" required>
				            <option value="">Select Municipality</option>
				            <?php
				            // Loop through the municipalities array to create options with municipality_name as value
				            foreach ($municipalities as $municipality) {
				                // Check if the current municipality matches the one from the session
				                $selected = ($municipality->municipality_name === $municipalityFromSession) ? 'selected' : '';
				                echo '<option value="' . htmlspecialchars($municipality->municipality_name) . '" ' . $selected . '>' . htmlspecialchars($municipality->municipality_name) . '</option>';
				            }
				            ?>
				        </select>
				    </div>
				</div>

                <!-- Barangay Dropdown -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="barangay_name">Barangay <span class="text-danger">*</span></label>
                        <select class="form-select" id="barangay_name" name="barangay_name" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </div>

                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="street">Street (Optional)</span></label>
                        <input type="text" class="form-control" name="street" value="<?php echo isset($_SESSION['form_data']['street']) ? htmlspecialchars($_SESSION['form_data']['street']) : ''; ?>" placeholder="e.g. St. P.I. Garcia">
                    </div>
                </div>

                <!-- Disease Dropdown -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="name_of_disease">Patient's Disease <span class="text-danger">*</span></label>
                        <select class="form-select" id="name_of_disease" name="name_of_disease" required>
                            <option value="">Select Disease</option>
                            <?php
                            // Loop through the diseases array to create options with name_of_disease as value
                            foreach ($diseases as $disease) {
                                $selected = (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] === $disease->name_of_disease) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($disease->name_of_disease) . '" ' . $selected . '>' . htmlspecialchars($disease->name_of_disease) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Map Integration -->
                <div class="row text-dark">
                    <div class="col-md-12 mb-4">
                        <label class="form-label" for="geometry">Geometry <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="geometry" name="geometry" placeholder="Draw on the map" readonly required hidden>
                        <small class="form-text text-muted">
                            Please select a location using the draw marker icon and ensure it is within the boundaries of the barangay geometry on the map.
                        </small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-municipality-user-add-cases" onclick="return confirm('Are you sure you want to submit? Please review the details before proceeding.');" style="justify-content: center !important;" title="Submit">Submit</button>
                    <a href="disease_data.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" style="justify-content: center !important;" title="Cancel">Cancel</a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to filter barangays based on selected municipality
    document.getElementById('municipality_name').addEventListener('change', function() {
        var selectedMunicipality = this.value;
        var barangaySelect = document.getElementById('barangay_name');

        // Clear existing options
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        // Clear the map and reset geometry field
        drawnItems.clearLayers();
        document.getElementById('geometry').value = '';

        // Reset map view to the original position
        map.setView([11.643319214056731, 124.47612493038605], 10);

        // Filter barangays based on selected municipality
        barangays.forEach(function(barangay) {
            if (barangay.municipality_name === selectedMunicipality) {
                var option = document.createElement('option');
                option.value = barangay.barangay_name;
                option.textContent = barangay.barangay_name;
                barangaySelect.appendChild(option);
            }
        });
    });

    // Capture selected barangay value on form submission
    document.querySelector('form').addEventListener('submit', function() {
        var selectedBarangay = document.getElementById('barangay_name').value;
        console.log('Selected Barangay:', selectedBarangay);  // You can store it or send to the server
    });
</script>

<script>
    var barangays = <?php echo json_encode($barangays); ?>;
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
            polygon: false,
            polyline: false,
            rectangle: false,
            circle: false,
            marker: true
        }
    });

    // Event listener for when a marker is created
    map.on('draw:created', function(e) {
        var layer = e.layer;
        drawnItems.addLayer(layer);

        // Extract coordinates for marker (lat, lng)
        var coordinates = [layer.getLatLng().lng, layer.getLatLng().lat];

        // Wrap the coordinates in an additional array to match the required format
        var geometryData = JSON.stringify([coordinates]);

        // Populate the hidden input field with the simplified geometry data
        document.getElementById('geometry').value = geometryData;
    });

    // Event listener for when a shape (marker) is deleted
    map.on('draw:deleted', function(e) {
        // Clear the geometry field when the marker is deleted
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

    // Update map view based on selected barangay's geometry
    document.getElementById('barangay_name').addEventListener('change', function() {
        var selectedBarangay = this.value;
        
        // Find the selected barangay from the data
        var barangay = barangays.find(function(b) {
            return b.barangay_name === selectedBarangay;
        });

        if (barangay && barangay.geom) {
            // Clear existing layers on map
            drawnItems.clearLayers();

            // Convert geometry (polygon) data to Leaflet LatLng array and add to map
            var latlngs = barangay.geom[0].map(function(coord) {
                return [coord[1], coord[0]]; // Invert coordinates for Leaflet (lat, lng => lng, lat)
            });

            // Create a polygon and add it to the map
            var polygon = L.polygon(latlngs, { color: 'blue' }).addTo(drawnItems);

            // Optionally, zoom the map to the bounds of the polygon
            map.fitBounds(polygon.getBounds());
        } else {
            // Handle case if no geometry data is available
            console.log('No geometry data found for the selected barangay.');
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
