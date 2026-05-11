<?php
include 'header.php';

// Get selected values from URL parameters for filtering
$municipality = isset($_GET['municipality_name']) ? $_GET['municipality_name'] : '';

// Check if 'All' option is selected for municipality
if ($municipality == 'All') {
    $municipality = ''; // If 'All' is selected, remove municipality filter
}

$email = $_SESSION['email'];
$municipalityFromSession = $function->getMunicipalityByEmail($email);

if (!$municipality && $municipalityFromSession) {
    $municipality = $municipalityFromSession;
}

$municipalities = $function->getAllMunicipalities();
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
            <h3 class="mb-4 text-dark">Barangays</h3>
            <a class="btn fw-bold mb-4 text-light" role="button" href="add_barangay.php" style="background-color: #006666">Add Barangay</a>

            <!-- Municipality Filter -->
            <div class="row mb-2">
                <div class="col-auto">
                    <!-- Municipality selection dropdown -->
                    <form method="GET" action="">
                        <div class="form-group text-dark mb-3">
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
                    </form>
                </div>
            </div>

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
                                    <th class="border-bottom-0 header text-light">Municipality</th>
                                    <th class="border-bottom-0 header text-light">Barangay Code</th>
                                    <th class="border-bottom-0 header text-light">Barangay</th>
                                    <th class="border-bottom-0 header text-light">No. of Population</th>
                                    <th class="border-bottom-0 header text-light">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <?php
                                $i = 0; // Initialize counter for ID
                                $barangays = $function->getFilteredBarangaysByMunicipality($municipality); // Pass selected municipality

                                if ($barangays) {
                                    foreach ($barangays as $barangay):
                                        $i++;
                                        $id = $barangay['id'];
                                        $municipality_name = $barangay['municipality_name'];
                                        $barangay_code = $barangay['barangay_code'];
                                        $barangay_name = $barangay['barangay_name'];
                                        $no_of_population = $barangay['no_of_population'];
                                ?>
                                        <tr class="text-align-center">
                                            <td class="text-center"><?= $i; ?></td>
                                            <td><?= $municipality_name; ?></td>
                                            <td class="text-center"><?= $barangay_code; ?></td>
                                            <td><?= $barangay_name; ?></td>
                                            <td class="text-center"><?= $no_of_population; ?></td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    <a class="btn btn-warning me-2" href="update_barangay.php?id=<?= $id; ?>" title="Edit Barangay">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action='../navigate.php' method='post' style='display: inline;'>
                                                        <input type='hidden' name='id' value='<?= $barangay['id']; ?>'>
                                                        <button class='btn btn-danger' name='btn-delete-barangay' type='submit' onclick="return confirm('Are you sure you want to delete this barangay?');" title='Delete Barangay'>
                                                            <i class='fa fa-trash'></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                    endforeach;
                                } else {
                                    // Display message if no barangays found
                                    echo "<tr><td colspan='6' class='text-center text-dark'>No barangays found</td></tr>";
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
<script src="../assets/js/app.js"></script>
<script>
    $(document).ready(function() {
        $('#table').DataTable();
    });
</script>

</body>
</html>
