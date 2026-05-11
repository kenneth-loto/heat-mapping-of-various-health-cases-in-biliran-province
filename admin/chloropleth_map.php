<?php
include 'header.php';

$disease = isset($_GET['name_of_disease']) ? $_GET['name_of_disease'] : '';  // Default to empty string
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : 'all';  // Default to empty string

// Fetch the barangay and case data based on the selected parameters
$barangay_and_case = $function->fetchBarangayAndCases($disease, $fromDate);

// Encode data as JSON for use in JavaScript
$barangays_json = json_encode($barangay_and_case);  // Correct variable name here

// Fetch distinct diseases for the dropdown
$diseases = $function->getDistinctDiseasesCases();

$total_all = isset($total_all) ? $total_all : 0;
$fromDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : '';

// Adjust the total_all based on the selected date range
if ($fromDate == 'all') {
    $total_all += 1; // Add 1 for total
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Borders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        #choromap {
            width: 100%;
            height: 600px;
        }
        .info {
            padding: 8px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }
        .leaflet-top.leaflet-left {
            position: absolute;
            bottom: 30px;
            right: 20px;
            left: auto;
            top: auto;
            z-index: 1000;
        }
        .info {
            padding: 15px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .info h4 {
            margin: 0 0 5px;
            color: #777;

        }
        .legend-card {
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            color: #000;
            font-size: medium;
            padding: 20px;
            border-radius: 3px;
        }
        .legend-card i {
            width: 18px;
            height: 20px;
            display: inline-block;
            margin-right: 8px;
            opacity: 0.7;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        .legend-card h4 {
            font-size: 16px;
            color: #333;
            text-align: center;
            margin-bottom: 10px;
        }
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .blink {
            animation: blink 1s infinite;
        }
        .form-container {
            position: absolute;
            top: 95px;
            left: 60px;
            z-index: 1000; /* Ensure the form is on top of the map */
            background-color: rgba(255, 255, 255, 0.8); /* Optional, to make the form more readable */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <div id="choromap"></div>

    <div class="form-container">
        <form method="GET" action="">
            <!-- Disease Selection Dropdown -->
            <div class="form-group text-dark mb-3">
                <label for="name_of_disease" class="mr-2">Select Disease</label>
                <select name="name_of_disease" id="name_of_disease" onchange="this.form.submit()" class="form-control">
                    <option value="">---------------</option>  
                    <?php foreach ($diseases as $diseaseOption): ?>
                        <option value="<?php echo $diseaseOption['name_of_disease']; ?>" 
                            <?php echo (isset($_GET['name_of_disease']) && $_GET['name_of_disease'] == $diseaseOption['name_of_disease']) ? 'selected' : ''; ?>>
                            <?php echo $diseaseOption['name_of_disease']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Date Range Selection Dropdown -->
            <div class="form-group text-dark">
                <label for="fromDate" class="mr-2">Select Date Range</label>
                <select name="fromDate" id="fromDate" onchange="this.form.submit()" class="form-control">
                    <option value="all" <?php echo (isset($_GET['fromDate']) && $_GET['fromDate'] == 'all') ? 'selected' : ''; ?>>Total</option>
                    <option value="last_7_days" <?php echo (isset($_GET['fromDate']) && $_GET['fromDate'] == 'last_7_days') ? 'selected' : ''; ?>>Last 7 Days</option>
                </select>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
    <script>
        // Initialize map
        var map = L.map('choromap').setView([11.6400, 124.4642], 11);

        // Add tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://carto.com/">CartoDB</a>'
        }).addTo(map);

        // Data from PHP: Barangays with geospatial data
        var barangays = <?php echo $barangays_json; ?>;

        // Function to get color based on the case count
        function getColor(d) {
            const total_7 = <?php echo isset($total_7) ? $total_7 : '0'; ?>;  // Example of PHP variable passed
            const total_30 = <?php echo isset($total_30) ? $total_30 : '0'; ?>;
            const total_all = <?php echo isset($total_all) ? $total_all : '0'; ?>;

            if (total_all != '') {
                return d > 1000 ? '#990000' :
                    d > 500 ? '#d7301f' :
                        d > 200 ? '#ef6548' :
                            d > 100 ? '#fc8d59' :
                                d > 50 ? '#fdbb84' :
                                    d > 10 ? '#fdd49e' :
                                        d > 0 ? '#fee8c8' :
                                            '#fff7ec';
            } else if (total_30 != '') {
                return d > 1000 ? '#990000' :
                    d > 500 ? '#d7301f' :
                        d > 200 ? '#ef6548' :
                            d > 100 ? '#fc8d59' :
                                d > 50 ? '#fdbb84' :
                                    d > 10 ? '#fdd49e' :
                                        d > 0 ? '#fee8c8' :
                                            '#fff7ec';
            } else {
                return d > 10 ? '#b10026' :
                    d > 6 ? '#e31a1c' :
                        d > 5 ? '#fc4e2a' :
                            d > 3 ? '#fd8d3c' :
                                d > 2 ? '#feb24c' :
                                    d > 1 ? '#fed976' :
                                        d > 0 ? '#ffffb2' :
                                            '#fff7ec';
            }
        }

        // Style function for each feature (barangay)
        function style(feature) {
            return {
                fillColor: getColor(feature.properties.case_count),  // Use case_count for color
                weight: .5,
                opacity: 1,
                color: '#666', // Border color
                fillOpacity: 0.6
            };
        }

        // Highlight feature on hover
        function highlightFeature(e) {
            var layer = e.target;

            // Highlight the feature
            layer.setStyle({
                weight: 3,
                color: '#6c757d',
                dashArray: '',
                fillOpacity: 0.8
            });

            // Bring the layer to the front
            layer.bringToFront();

            // Update the info control with the barangay and municipality names
            info.update(layer.feature.properties);
        }

        // Reset highlight on mouseout
        function resetHighlight(e) {
            geojson.resetStyle(e.target);

            // Clear the info control text
            info.update();
        }

        // Zoom to feature on click
        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        // Adding interaction
        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });
        }

        // Add GeoJSON layer
        var geojson = L.geoJson(barangays, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        // Info control
        var info = L.control();
            info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };

        // Update info control
        info.update = function (props) {
            // Replace the template placeholders with the actual PHP values
            var total_all = <?php echo $total_all; ?>;
            var total_7 = <?php echo isset($total_7) ? $total_7 : '0'; ?>;
            var total_30 = <?php echo isset($total_30) ? $total_30 : '0'; ?>;

            // Initially display hover message right away, regardless of props
            if (!props) {
                this._div.innerHTML = '<span class="text-center text-secondary">Hover over a barangay to see details</span>';
            }

            // Display details right away if props are available
            if (props) {
                const isAllSelected = (total_all && total_all !== 0); // Check if 'All' is selected

                this._div.innerHTML = 
                    '<b><h4 class="text-center fw-bold text-dark">' + props.barangay_name + ', ' + props.municipality_name + '</h4><br></b>' +
                    '<p class="text-danger text-center" style="font-size: 18px;">' +
                        '<span class="fw-bold">' + props.case_count + '</span> ' +
                        '<span class="text-secondary">confirmed cases</span>' +
                    '</p>' +
                    // Only show comparison text if 'Last 7 Days' is selected, not 'All'
                    (!isAllSelected ? 
                        (props.case_count >= 10 ? 
                            '<h3 class="text-danger text-center fw-bold blink">An Outbreak Alert!</h3><br><p class="text-center text-secondary">Please take action immediately!</p>' : 
                        (props.case_count >= 3 ? 
                            '<h3 class="text-warning text-center fw-bold">Possible Outbreak</h3><br><p class="text-center text-secondary">Please take action to prevent it from spreading further.</p>' : '')
                    ) : ''
                    );
            }
        };

        info.addTo(map);

        // Legend control with card styling
        var legend = L.control({ position: 'bottomleft' });

        legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'legend-card'); // Create legend container
        
        // Get PHP variables
        const total_7 = <?php echo isset($total_7) ? $total_7 : '0'; ?>;
        const total_30 = <?php echo isset($total_30) ? $total_30 : '0'; ?>;
        const total_all = <?php echo isset($total_all) ? $total_all : '0'; ?>;
        
        // Adjust the total_all based on the selected date range logic (assumes $fromDate is set in PHP)
        <?php
        if ($fromDate == 'all') {
            $total_all += 1; // Increment total for 'all' case
        }
        ?>

        // Use JavaScript condition to display the correct legend based on total_all adjustment
        if (total_all > 0) {
            // Display legend for 'All' (when $fromDate is 'all')
            div.innerHTML += '<center class="mb-3">Cases</center>' +
                '<i style="background:#990000; align-items: center;"></i> 1000+<br>' +
                '<i style="background:#d7301f"></i> 501&ndash;1000<br>' +
                '<i style="background:#ef6548"></i> 201&ndash;500<br>' +
                '<i style="background:#fc8d59"></i> 101&ndash;200<br>' +
                '<i style="background:#fdbb84"></i> 51&ndash;100<br>' +
                '<i style="background:#fdd49e"></i> 11&ndash;50<br>' +
                '<i style="background:#fee8c8"></i> 1&ndash;10<br>' +
                '<i style="background:#fff7ec"></i> 0<br>';
        } else {
            // Display legend for 'Last 7 Days'
            div.innerHTML += '<center class="mb-3">Cases</center>' +
                '<i style="background:#b10026"></i> 10+<br>' +
                '<i style="background:#e31a1c"></i> 6<br>' +
                '<i style="background:#fc4e2a"></i> 5<br>' +
                '<i style="background:#fd8d3c"></i> 4<br>' +
                '<i style="background:#feb24c"></i> 3<br>' +
                '<i style="background:#fed976"></i> 2<br>' +
                '<i style="background:#ffffb2"></i> 1<br>' +
                '<i style="background:#fff7ec"></i> 0<br>';
        }
        
        return div;
    };

        legend.addTo(map);
    </script>
</body>
</html>
