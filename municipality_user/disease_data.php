<?php
include 'header.php';

// Get selected values from URL parameters for filtering
$disease = isset($_GET['name_of_disease']) ? $_GET['name_of_disease'] : '';
$municipality = isset($_GET['municipality']) ? $_GET['municipality'] : '';

// Fetch the municipality for the logged-in user from the session email
$email = $_SESSION['email'];
$municipalityFromSession = $function->getMunicipalityByEmail($email);

// If no municipality is selected, use the one from the session
if (!$municipality && $municipalityFromSession) {
    $municipality = $municipalityFromSession;
}

// Check if 'All' option is selected for disease
if ($disease == 'All') {
    $disease = ''; // If 'All' is selected, remove disease filter
}

$municipalities = $function->getMunicipalities();
$diseases = $function->getDiseases();  // Call method
?>

<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Include DataTables and Bootstrap -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
<script defer src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>

<!-- Custom CSS -->
<link rel="stylesheet" href="../assets/css/tablee.css">
<link rel="stylesheet" href="../assets/css/requestss.css">

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-4 text-dark">Diseases Data</h3>
            <a class="btn fw-bold mb-4 text-light" role="button" href="add_patient.php" style="background-color: #006666">Add Patient</a>

            <!-- Combined Disease and Municipality Filters -->
            <form method="GET" action="">
                <div class="row mb-2">

                    <div class="col-auto">
                        <!-- Municipality selection dropdown -->
                        <div class="form-group text-dark mb-3">
                            <label for="municipality" class="mr-2">Select Municipality:</label>
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
                    </div>

                    <div class="col-auto">
                        <!-- Disease selection dropdown -->
                        <div class="form-group text-dark mb-4">
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
                    </div>
                    
                </div>
            </form>

            <?php
            // Display any messages from session
            $msg = Session::get("msg");
            if (isset($msg)) {
                echo $msg;
                Session::set("msg", NULL);
            }
            ?>

            <div class="col-lg-12 d-flex align-items-stretch text-dark">
                <div class="w-100">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table">
                            <thead class="text-dark">
                                <tr>
                                    <th class="border-bottom-0 header text-light">ID</th>
                                    <th class="border-bottom-0 header text-light">Patient's ID</th>
                                    <th class="border-bottom-0 header text-light">Municipality</th>
                                    <th class="border-bottom-0 header text-light">Barangay</th>
                                    <th class="border-bottom-0 header text-light">Disease</th>
                                    <th class="border-bottom-0 header text-light">Date</th>
                                    <th class="border-bottom-0 header text-light">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <?php
                                $i = 0; // Initialize counter for ID
                                $patients = $function->getFilteredPatientsByMunicipality($disease, $municipality); // Fetch filtered disease data

                                if ($patients) {
                                    foreach ($patients as $patient) :
                                        $i++;
                                        $id = $patient['id'];
                                        $patients_id = $patient['patients_id'];
                                        $municipality_name = $patient['municipality_name'];
                                        $barangay_name = $patient['barangay_name'];
                                        $name_of_disease = $patient['name_of_disease'];
                                        $created_at = $patient['created_at'];
                                ?>
                                        <tr class="text-align-left">
                                            <td class="text-center"><?= $i; ?></td>
                                            <td><?= $patients_id; ?></td>
                                            <td><?= $municipality_name; ?></td>
                                            <td><?= $barangay_name; ?></td>
                                            <td><?= $name_of_disease; ?></td>
                                            <td><?= date('F j, Y, g:i a', strtotime($created_at)); ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a class="btn btn-warning me-2" href="update_patient.php?id=<?= $id; ?>" title="Edit Disease">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action='../navigate.php' method='post' style='display: inline;'>
                                                        <input type='hidden' name='id' value='<?= $patient['id']; ?>'>
                                                        <button class='btn btn-danger' name='btn-delete-patient' type='submit' onclick="return confirm('Are you sure you want to delete this disease?');" title='Delete Request'>
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    endforeach;
                                } else {
                                    // Display message if no diseases found
                                    echo "<tr><td colspan='7' class='text-center text-dark'>No diseases found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

<script>
    // Refresh the page after changing filters
    $(document).ready(function() {
        $('#table').DataTable();
    });
</script>
