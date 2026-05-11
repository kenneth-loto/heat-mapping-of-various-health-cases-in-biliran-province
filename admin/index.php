<?php
include 'header.php';
include_once '../conn.php';

// Create an instance of the Functions class
$functions = new Functions();

// Get selected disease and municipality
$disease = isset($_GET['name_of_disease']) ? $_GET['name_of_disease'] : '';  // Set default to empty string
$municipality = isset($_GET['municipality_name']) ? $_GET['municipality_name'] : '';

// Fetch distinct municipalities
$municipalities = $functions->getDistinctMunicipalitiesCases();  // Call method

// Fetch distinct diseases
$diseases = $functions->getDistinctDiseasesCases();  // Call method

// Fetch patient data based on disease, municipality, and date range
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : ''; 
$toDate = isset($_GET['toDate']) ? $_GET['toDate'] : ''; 

// Use the $fromDate and $toDate in your function calls
$patients = $functions->getPatientsByDiseaseCases($disease, $municipality, $fromDate, $toDate);

// If no disease, municipality, or date range is selected, set $numCases to 0
if (empty($disease) && empty($municipality) && empty($fromDate) && empty($toDate)) {
    $numCases = 0;
} else {
    // Fetch the number of cases based on the selected disease, municipality, and date range
    $numCases = $functions->getNumOfCases($disease, $municipality, $fromDate, $toDate);
}

$patientsJson = json_encode($patients);

// Fetch heatmap data based on the retrieved patient data
$heatmapData = $functions->getHeatmapData($patients);

$shouldDisplayMap = !empty($municipality) || !empty($disease) || !empty($fromDate) || !empty($toDate);
$patientsJson = $shouldDisplayMap ? json_encode($heatmapData) : json_encode([]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
    <script src="https://unpkg.com/leaflet-choropleth/dist/leaflet-choropleth.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        #map {
            height: 550px;
            width: 100%;
            position: relative;
            border-radius: 0;
        }

        #legend {
            background: transparent; /* Make legend background transparent */
            padding: 0;
            position: absolute;
            bottom: 10px;
            left: 10px;
            z-index: 1000;
            width: 150px; /* Adjust width as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid white;
        }

        .filters-container {
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        }

        .map-container {
            position: relative;
        }

        .dropdown-container {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1001;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        }

        .cases-card {
            position: absolute;
            top: 10px;
            right: 770px;
            z-index: 1002;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
            font-size: 14px;
            width: 150px;
            text-align: center;
        }

        .leaflet-top.leaflet-left {
            position: absolute;
            bottom: 30px;
            right: 20px;
            left: auto;
            top: auto;
            z-index: 1000;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-control {
            width: 100%;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .horizontal-status-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 5px;
            background: linear-gradient(to right, blue, green, yellow, red);
            border-radius: 5px;
            height: 20px; /* Adjust height as needed */
            position: relative;
            height: 40px;
        }

        .status-label {
            color: white;
            font-weight: bold;
            font-size: 11px;
            margin: 0 5px;
            position: absolute;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.6); /* Improve text readability */
        }

        .status-label:first-child {
            left: 5px;
        }

        .status-label:nth-child(3) {
            left: 45%;
            transform: translateX(-50%);
        }

        .status-label:last-child {
            right: 5px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Filter Section (Left Side) -->
        <div class="col-md-2">
            <div class="filters-container">

            <h4 class="text-dark">Cases</h4>

                <!-- Combined Form for Municipality, Disease, and Date Filters -->
                <!-- Combined Form for Municipality, Disease, and Date Filters -->
                <form method="GET" action="">
                    <div class="form-group text-dark mb-3">
                        <label for="municipality_name" class="mr-2">Select Municipality</label>
                        <select name="municipality_name" id="municipality_name" onchange="this.form.submit()" class="form-control">
                            <option value="">--------------------</option>
                            <?php foreach ($municipalities as $municipalityOption): ?>
                                <option value="<?php echo $municipalityOption['municipality_name']; ?>" 
                                    <?php echo (isset($_GET['municipality_name']) && $_GET['municipality_name'] == $municipalityOption['municipality_name']) ? 'selected' : ''; ?>>
                                    <?php echo $municipalityOption['municipality_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group text-dark mb-4">
                        <label for="name_of_disease" class="mr-2">Select Disease</label>
                        <select name="name_of_disease" id="name_of_disease" onchange="this.form.submit()" class="form-control">
                            <option value="">--------------------</option>
                            <?php foreach ($diseases as $diseaseOption): ?>
                                <option value="<?php echo $diseaseOption['name_of_disease']; ?>" 
                                    <?php echo (isset($_GET['name_of_disease']) && $_GET['name_of_disease'] == $diseaseOption['name_of_disease']) ? 'selected' : ''; ?>>
                                    <?php echo $diseaseOption['name_of_disease']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <h4 class="text-dark">Select Date</h4>
                    <label for="fromDate" class="mr-2 text-dark">From</label>
                    <input type="date" name="fromDate" id="fromDate" class="form-control text-dark" value="<?php echo isset($_GET['fromDate']) ? $_GET['fromDate'] : ''; ?>" onchange="updateToDate()" />

                    <label for="toDate" class="mr-2 mt-2 text-dark">To</label>
                    <input type="date" name="toDate" id="toDate" class="form-control text-dark" value="<?php echo isset($_GET['toDate']) ? $_GET['toDate'] : ''; ?>" />

                    <div class="d-flex flex-column mt-3 align-items-center">
                        <button type="submit" class="btn text-light mb-2" style="background-color: #006666; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-filter me-2"></i> Filter
                        </button>
                        <button type="button" onclick="resetDates()" class="btn text-light" style="background-color: #006666; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-redo me-2"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Map Section (Right Side) -->
        <div class="col-md-10">
            <div class="map-container">
                <!-- Card for Number of Cases -->
                <div class="cases-card text-dark">
                    <h4>Number of Cases</h4>
                    <div id="cases-count">
                        <?php echo $numCases; ?>
                    </div>
                </div>

                <!-- Map View Dropdown (Inside Map) -->
                <div class="dropdown-container">
                    <div class="form-group text-dark mb-3 text-center">
                        <label for="layerView" class="mr-2">Layer</label>
                        <select id="layerView" class="form-control border-dark">
                            <option value="heatmap" selected>Heatmap</option>
                       <!--     <option value="pointmap">Point Map</option> -->
                        </select>
                    </div>
                    <div class="form-group text-dark text-center">
                        <label for="mapView" class="mr-2">Map View</label>
                        <select id="mapView" class="form-control border-dark">
                            <option value="dark" selected>Dark Map</option>
                            <option value="light">Light Map</option>
                            <option value="street">Street Map</option>
                        </select>
                    </div>
                </div>

                <!-- Legend inside the map (bottom-left) -->
                <div id="legend" class="text-dark">
                    <div class="horizontal-status-bar">
                        <span class="status-label">Stable</span>
                        <span class="status-gradient"></span>
                        <span class="status-label">Moderate</span>
                        <span class="status-gradient"></span>
                        <span class="status-label">Critical</span>
                    </div>
                </div>

                <!-- Map Container -->
                <div id="map"></div>
                
            </div>
        </div>
    </div>
</div>


<script>
    // Define the maximum bounds for the map
    var maxBounds = L.latLngBounds(
        L.latLng(11.358607609157232, 123.91744935882099), // Southwest corner
        L.latLng(11.897821676214718, 125.01560057070333)  // Northeast corner
    );

    // Initialize the map
    var map = L.map('map', {
        doubleClickZoom: false,
        maxZoom: 19,
        minZoom: 10,
    }).setView([11.6400, 124.4642], 11).setMaxBounds(maxBounds);

    // Event listener for double-click to reset zoom
    map.on('dblclick', function (e) {
        map.setZoom(10);
    });

    // Load the base tile layers for different views
    var lightLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://carto.com/">CartoDB</a>'
    });

    var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var darkLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://carto.com/">CARTO</a>'
    });

    // Function to update the map view based on selected value
    function updateMapView(selectedView) {
        if (selectedView === 'light') {
            map.addLayer(lightLayer);
            map.removeLayer(streetLayer);
            map.removeLayer(darkLayer);
        } else if (selectedView === 'street') {
            map.addLayer(streetLayer);
            map.removeLayer(lightLayer);
            map.removeLayer(darkLayer);
        } else if (selectedView === 'dark') {
            map.addLayer(darkLayer);
            map.removeLayer(lightLayer);
            map.removeLayer(streetLayer);
        }
    }

    // Get the saved map view from localStorage (if any)
    var savedMapView = localStorage.getItem('mapView') || 'light'; // Default to 'light' view
    document.getElementById('mapView').value = savedMapView; // Set dropdown to saved value
    updateMapView(savedMapView); // Apply saved map view

    // Event listener to handle map view changes
    document.getElementById('mapView').addEventListener('change', function() {
        var selectedView = this.value;
        localStorage.setItem('mapView', selectedView); // Save the selected view to localStorage
        updateMapView(selectedView); // Update the map view
    });

    // Heatmap data - PHP to JavaScript array
    const heatmapData = <?php echo $patientsJson; ?>; // Using the updated PHP variable

    // Create heatmap layer with parsed latitude/longitude values and intensity
    var heatLayer = L.heatLayer(heatmapData.map(data => [
        parseFloat(data.latitude),   // Latitude
        parseFloat(data.longitude),  // Longitude
        data.intensity               // Intensity
    ]), {
        radius: 3, // Default radius
        blur: 2, // A smooth but noticeable effect
        minOpacity: 1, // Slightly lower for better visibility of low-intensity areas
        max: 1,
        maxZoom: 11,
        gradient: {
            0.10: 'blue',    // Lowest intensity
            0.70: 'green',  // Mid intensity
            0.95: 'yellow', // Slightly higher intensity
            0.98: 'red'      // Highest intensity
        }
    }).addTo(map);

    // Function to scale the heatmap layer based on zoom level
    function scaleHeatmapOnZoom() {
        var zoomLevel = map.getZoom();

        // Scale the heatmap radius proportionally to the zoom level
        var scale = Math.pow(2, zoomLevel - 10);  // Adjust this power factor as needed
        heatLayer.setOptions({
            radius: 3 * scale,  // Radius increases as you zoom in
            blur: 2 * scale,    // Blur also scales with zoom
            minOpacity: 1,
        });
    }

    // Call the scale function initially to adjust heatmap
    scaleHeatmapOnZoom();

    // Update heatmap scale whenever the zoom level changes
    map.on('zoomend', function() {
        scaleHeatmapOnZoom(); // Adjust heatmap scale on zoom change
    });

    // Create marker layer for point map
    var pointMapLayer = L.layerGroup();

    // Function to update the map layer based on selected layer (Heatmap or Point Map)
    function updateMapLayer(selectedLayer) {
        if (selectedLayer === 'heatmap') {
            updateMapView('dark');
            // Add heatmap layer
            map.addLayer(heatLayer);
            map.removeLayer(pointMapLayer);  // Remove point map layer
        } else if (selectedLayer === 'pointmap') {
            // Automatically change to the 'light' map view for point map
            updateMapView('light'); // Switch to the light layer
            // Add point map layer (assuming points are based on the same data)
            pointMapLayer.clearLayers();  // Clear any existing markers
            heatmapData.forEach(function(data) {
                var marker = L.circleMarker([parseFloat(data.latitude), parseFloat(data.longitude)], {
                    radius: 3, // Smaller radius for point map (even smaller)
                    fillColor: 'red',
                    stroke: false,
                    fillOpacity: 1,  // Slightly more opaque to highlight points
                }).bindPopup("Intensity: " + data.intensity);
                pointMapLayer.addLayer(marker);
            });
            map.addLayer(pointMapLayer);
            map.removeLayer(heatLayer);  // Remove heatmap layer
        }
    }

    // Get the saved map layer from localStorage (if any)
    var savedLayerView = localStorage.getItem('layerView') || 'heatmap'; // Default to 'heatmap' layer
    document.getElementById('layerView').value = savedLayerView; // Set dropdown to saved value
    updateMapLayer(savedLayerView); // Apply saved layer view

    // Event listener to handle layer view changes
    document.getElementById('layerView').addEventListener('change', function() {
        var selectedLayer = this.value;
        localStorage.setItem('layerView', selectedLayer); // Save the selected layer to localStorage
        updateMapLayer(selectedLayer); // Update the map layer
    });

    // Get the number of cases from PHP and update the cases-count element
    document.getElementById('cases-count').innerText = "<?php echo $numCases; ?>";
</script>

<script>
    function resetDates() {
        // Reset the date fields to empty (or default values)
        document.getElementById('fromDate').value = '';
        document.getElementById('toDate').value = '';
        
        // Optionally, you can submit the form after resetting dates to refresh the page without those filters
        document.querySelector('form').submit();
    }
</script>

<script>
    // Function to update the min date for the "to" input based on the "from" input
    function updateToDate() {
        var fromDate = document.getElementById('fromDate').value;
        var toDate = document.getElementById('toDate');

        // If a "from date" is selected, set the "to date" min value to the "from date"
        if (fromDate) {
            toDate.setAttribute('min', fromDate);
        } else {
            // If no "from date" is selected, remove the "min" attribute for the "to date"
            toDate.removeAttribute('min');
        }
    }

    // Call the function once on page load to ensure proper handling if a date is pre-selected
    document.addEventListener('DOMContentLoaded', updateToDate);
</script>

</body>
</html>
