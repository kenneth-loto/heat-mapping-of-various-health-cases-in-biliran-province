<?php
include 'header.php';
include_once '../conn.php';

// Create an instance of the Functions class
$functions = new Functions();

// Get selected disease, municipality, and time period
$municipality = isset($_GET['municipality_name']) ? $_GET['municipality_name'] : '';
$time_period = isset($_GET['time_period']) ? $_GET['time_period'] : 'none';  // Default to 'none' if not set

// Fetch distinct municipalities and diseases
$municipalities = $functions->getDistinctMunicipalitiesCases();  // Call method to get municipalities
$diseases = $functions->getDistinctDiseasesCases();  // Call method to get diseases

// Fetch the municipality for the logged-in user from the session email
$email = $_SESSION['email'];
$municipalityFromSession = $function->getMunicipalityByEmail($email);

// If no municipality is selected, use the one from the session
if (!$municipality && $municipalityFromSession) {
    $municipality = $municipalityFromSession;
}

// Initialize chartData as null for when 'none' is selected
$chartData = null;

// Fetch data for the specific time period and disease (if selected)
if ($time_period == 'none') {
    // If "------" (none) is selected, do not display any data
    $chartData = null;
} elseif ($time_period == '') {
    // If "All Data" is selected, display all data
    $chartData = $functions->getChartDataByTimePeriod($municipality, 'all', isset($_GET['name_of_disease']) ? $_GET['name_of_disease'] : '');
} else {
    // Otherwise, fetch data for the specific time period (yearly, monthly, etc.) and disease
    $chartData = $functions->getChartDataByTimePeriod($municipality, $time_period, isset($_GET['name_of_disease']) ? $_GET['name_of_disease'] : '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Include necessary styles and scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-3 text-black">Cases</h5>

            <!-- Filter Form (Municipality, Disease, Time Period) -->
            <form method="GET" action="" class="row mb-4">
                <!-- Municipality Select -->
                <div class="form-group col-md-2 text-dark mb-3">
                    <label for="municipality_name" class="mr-2">Select Municipality</label>
                    <select name="municipality" id="municipality" onchange="this.form.submit()" class="form-control w-auto" style="pointer-events: none;">
                                <option value="">All</option>
                                <?php foreach ($municipalities as $municipalityOption): ?>
                                    <option value="<?php echo $municipalityOption['municipality_name']; ?>" 
                                        <?php echo ($municipality == $municipalityOption['municipality_name'] || $municipalityFromSession == $municipalityOption['municipality_name']) ? 'selected' : ''; ?>>
                                        <?php echo $municipalityOption['municipality_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                </div>

                <!-- Disease Select -->
                <div class="form-group col-md-2 text-dark mb-3">
                    <label for="name_of_disease" class="mr-2">Select Disease</label>
                    <select name="name_of_disease" id="name_of_disease" onchange="this.form.submit()" class="form-control">
                        <option value="">All</option>
                        <?php foreach ($diseases as $diseaseOption): ?>
                            <option value="<?php echo $diseaseOption['name_of_disease']; ?>" 
                                <?php echo (isset($_GET['name_of_disease']) && $_GET['name_of_disease'] == $diseaseOption['name_of_disease']) ? 'selected' : ''; ?>>
                                <?php echo $diseaseOption['name_of_disease']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Time Period Dropdown -->
                <div class="form-group col-md-2 text-dark mb-3">
                    <label for="time_period" class="mr-2">Select Time Period</label>
                    <select name="time_period" id="time_period" onchange="this.form.submit()" class="form-control">
                        <option value="none" <?php echo ($time_period == 'none') ? 'selected' : ''; ?>>-------------------------</option>
                        <option value="yearly" <?php echo ($time_period == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                        <option value="monthly" <?php echo ($time_period == 'monthly') ? 'selected' : ''; ?>>Monthly</option>
                        <option value="weekly" <?php echo ($time_period == 'weekly') ? 'selected' : ''; ?>>Weekly</option>
                        <option value="daily" <?php echo ($time_period == 'daily') ? 'selected' : ''; ?>>Daily</option>
                    </select>
                </div>
            </form>

            <!-- Display Disease Data in Charts -->
            <?php if ($municipality && $time_period != 'none' && $chartData !== null): ?>
                <div class="mb-4">
                    <h3 class="fw-semibold text-dark mb-3">Disease Cases in <?php echo $municipality; ?> (Time Period: <?php echo ucfirst($time_period); ?>)</h3>
                    
                    <!-- Loop through the diseases to display charts -->
                    <?php foreach ($chartData['diseases'] as $disease): ?>
                        <h4 class="fw-semibold text-dark"><?php echo $disease; ?></h4>
                        <div class="text-dark" id="chart_<?php echo $disease; ?>"></div>
                        
                        <script>
                            // Prepare data for ApexCharts
                            var chartData = {
                                values: <?php echo json_encode(array_values($chartData['values'])); ?>,
                                categories: <?php echo json_encode($chartData['categories']); ?>
                            };

                            // Initialize ApexCharts
                            var options = {
                                chart: {
                                    type: 'bar',
                                    height: 400
                                },
                                colors: ['#006666'],  // Set bar color here
                                series: [{
                                    name: 'Disease Cases',
                                    data: chartData.values.map(function(item) { 
                                        return item['<?php echo $disease; ?>'] || 0; 
                                    })
                                }],
                                xaxis: {
                                    categories: chartData.categories
                                }
                            };

                            var chart = new ApexCharts(document.querySelector("#chart_<?php echo $disease; ?>"), options);
                            chart.render();
                        </script>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.js"></script>

</body>
</html>
