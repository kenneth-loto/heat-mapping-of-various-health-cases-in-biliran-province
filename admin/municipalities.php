<?php
include 'header.php';
unset($_SESSION['form_data']);

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit; // Stop further execution
}
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
            <h5 class="card-title fw-semibold mb-4 text-dark">Municipalities</h5>
            <a class="btn fw-bold mb-3 text-light" role="button" href="add_municipality.php" style="background-color: #006666">Add Municipality</a>
            
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
                                    <th class="border-bottom-0 header text-light">Code</th>
                                    <th class="border-bottom-0 header text-light">Municipality</th>
                                    <th class="border-bottom-0 header text-light">Number of Population</th>
                                    <th class="border-bottom-0 header text-light">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <?php
                                $i = 0; // Initialize counter for ID
                                $municipalities = $function->getAllTheMunicipalities(); // Fetch disease data

                                if ($municipalities) {
                                    foreach ($municipalities as $municipality) :
                                        $i++;
                                        $id = $municipality['id'];
                                        $municipal_code = $municipality['municipal_code'];
                                        $municipality_name = $municipality['municipality_name'];
                                        $no_of_population = $municipality['no_of_population'];
                                ?>
                                        <tr class="text-align-left">
                                            <td class="text-center"><?= $i; ?></td>
                                            <td class="text-center"><?= $municipal_code; ?></td>
                                            <td><?= $municipality_name; ?></td>
                                            <td class="text-center"><?= $no_of_population; ?></td>
                                            <td>
                                                <div  class="d-flex justify-content-center">
                                                    <a class="btn btn-warning me-2" href="update_municipality.php?id=<?= $id; ?>" title="Edit Municipality">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action='../navigate.php' method='post' style='display: inline;'>
                                                        <input type='hidden' name='id' value='<?= $municipality['id']; ?>'>
                                                        <button class='btn btn-danger' name='btn-delete-municipality' type='submit' onclick="return confirm('Are you sure you want to delete this disease?');" title='Delete Request'>
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
                                    echo "<tr><td colspan='5' class='text-center text-dark'>No diseases found</td></tr>";
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
        $('#table').DataTable(); // Initialize DataTables
    });
</script>
