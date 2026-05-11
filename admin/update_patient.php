<?php
include 'header.php';

// Get patient ID from the query string
$id = $_GET['id'];

// Fetch the patient data (assuming getAPatient returns an associative array)
$patient = $function->getACase($id);

$municipalities = $function->getMunicipalitiesDB(); // Fetch municipalities
$barangays = $function->getBarangaysDB(); // Fetch barangays
$diseases = $function->getNameOfDiseases();
$barangays = $function->getAllBarangaysGeom(); // Fetch barangays with geom data
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
            <h3 class="text-center fw-bold mt-4 mb-4 text-dark">Edit Disease</h3>

            <?php
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="border p-3 mb-4" style="border: 1px solid #ccc; border-radius: 8px; background-color: #e9ecef;">
                <form method="post" action="../navigate.php" enctype="multipart/form-data" id="editPatientForm">
                    <div class="row text-dark">
                        <input type="hidden" name="id" value="<?= $patient->id; ?>" />

                        <!-- Name of Disease -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="patients_id">Patient's ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="patients_id" value="<?= isset($_SESSION['form_data']['patients_id']) ? htmlspecialchars($_SESSION['form_data']['patients_id']) : htmlspecialchars($patient->patients_id); ?>" placeholder="e.g. Diabetes" required>
                        </div>

                        <!-- Municipality Dropdown -->
                        <div class="row text-dark">
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="municipality_name">Municipality <span class="text-danger">*</span></label>
                                <select class="form-select" id="municipality_name" name="municipality_name" required>
                                    <option value="">Select Municipality</option>
                                    <?php
                                    foreach ($municipalities as $municipality) {
                                        $selected = $municipality->municipality_name === $patient->municipality_name ? 'selected' : '';
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

                        <!-- Name of Disease -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="street">Street (Optional) </span></label>
                            <input type="text" class="form-control" name="street" value="<?= isset($_SESSION['form_data']['street']) ? htmlspecialchars($_SESSION['form_data']['street']) : htmlspecialchars($patient->street); ?>" placeholder="e.g. Diabetes" required>
                        </div>

                        <!-- Patient's Disease -->
                        <div class="row text-dark">
                            <div class="col-md-12 mb-4">
                                <label class="form-label" for="name_of_disease">Patient's Disease<span class="text-danger">*</span></label>
                                <select class="form-select" name="name_of_disease" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="Diabetes" <?= (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] == 'Diabetes') || $patient->name_of_disease == 'Diabetes' ? 'selected' : ''; ?>>Diabetes</option>
                                    <option value="HIV" <?= (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] == 'HIV') || $patient->name_of_disease == 'HIV' ? 'selected' : ''; ?>>HIV</option>
                                    <option value="Monkey Fox" <?= (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] == 'Monkey Fox') || $patient->name_of_disease == 'Monkey Fox' ? 'selected' : ''; ?>>Monkey Fox</option>
                                    <option value="Pneumonia" <?= (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] == 'Pneumonia') || $patient->name_of_disease == 'Pneumonia' ? 'selected' : ''; ?>>Pneumonia</option>
                                    <option value="Tuberculosis" <?= (isset($_SESSION['form_data']['name_of_disease']) && $_SESSION['form_data']['name_of_disease'] == 'Tuberculosis') || $patient->name_of_disease == 'Tuberculosis' ? 'selected' : ''; ?>>Tuberculosis</option>
                                </select>
                            </div>
                        </div>

                        <!-- Geometry -->
                        <div class="col-md-12 mb-4">
                            <label class="form-label" for="geometry">Geometry <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="geometry" name="geometry" value="<?= htmlspecialchars($patient->geom); ?>" placeholder="Draw on the map" readonly required>
                            <small class="form-text text-muted">
                                Please select a location using the draw marker icon and ensure it is within the boundaries of the barangay geometry on the map.
                            </small>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map" style="height: 400px;"></div>
                            </div>
                        </div>

                        <!-- Centered Buttons -->
                        <div class="d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn btn-primary me-3 w-25 py-2 fs-4 mb-4 rounded-2" name="btn-update-patient" onclick="return confirm('Are you sure you want to submit? Please review your details before proceeding.');" title="Submit" style="justify-content: center !important;">Submit</button>
                            <a href="diseases.php" class="btn btn-danger w-25 py-2 fs-4 mb-4 rounded-2" title="Cancel" style="justify-content: center !important;">Cancel</a>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to filter and populate barangays based on selected municipality
    function filterBarangays(selectedMunicipality, preSelectedBarangay = null) {
        var barangaySelect = document.getElementById('barangay_name');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';

        // Filter barangays based on selected municipality
        var filteredBarangays = <?php echo json_encode($barangays); ?>;
        filteredBarangays.forEach(function(barangay) {
            if (barangay.municipality_name === selectedMunicipality) {
                var option = document.createElement('option');
                option.value = barangay.barangay_name;
                option.textContent = barangay.barangay_name;

                // Set as selected if it matches the pre-selected value
                if (preSelectedBarangay && barangay.barangay_name === preSelectedBarangay) {
                    option.selected = true;
                }

                barangaySelect.appendChild(option);
            }
        });
    }

    // On page load, filter barangays based on the patient's pre-selected municipality
    document.addEventListener('DOMContentLoaded', function() {
        var initialMunicipality = document.getElementById('municipality_name').value;
        var initialBarangay = '<?= $patient->barangay_name; ?>'; // Pre-selected barangay
        if (initialMunicipality) {
            filterBarangays(initialMunicipality, initialBarangay);
            updateMap(initialBarangay); // Update the map for the pre-selected barangay
        }
    });

    // When the municipality dropdown changes, trigger filtering
    document.getElementById('municipality_name').addEventListener('change', function() {
        filterBarangays(this.value);

        // Reset map view to the original position
        map.setView([11.643319214056731, 124.47612493038605], 10);

         // Clear the geometry field (hidden input) when the barangay is changed
         document.getElementById('geometry').value = '';

        // Clear any existing geometry on the map
        drawnItems.clearLayers();
    });
</script>

<script>
    var barangays = <?php echo json_encode($barangays); ?>;
    var patientLocation = null; // Default to null if geometry is not available

    // Check if the patient has a geometry and extract the location
    if ('<?= htmlspecialchars($patient->geom); ?>' !== '') {
        var patientGeometry = JSON.parse('<?= htmlspecialchars($patient->geom); ?>');
        if (patientGeometry && patientGeometry.length > 0) {
            var coordinates = patientGeometry[0]; // Extract the first coordinate (lat, lng)
            patientLocation = [coordinates[1], coordinates[0]]; // Invert to lat, lng for Leaflet
        }
    }

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

    // Function to update the map based on the selected barangay's geometry
    function updateMap(selectedBarangay) {
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

            // Draw patient's location on top of the barangay polygon
            if (patientLocation) {
                L.marker(patientLocation).addTo(map).bindPopup('Patient Location');
            }
        } else {
            // Handle case if no geometry data is available
            console.log('No geometry data found for the selected barangay.');
        }
    }

    // When the barangay dropdown changes, clear the current geometry and update the map
    document.getElementById('barangay_name').addEventListener('change', function() {
        var selectedBarangay = this.value;
        
        // Clear the geometry field (hidden input) when the barangay is changed
        document.getElementById('geometry').value = '';

        // Clear any existing geometry on the map
        drawnItems.clearLayers();

        // Update the map with the new barangay's geometry
        updateMap(selectedBarangay); 
    });
</script>

</body>
</html>