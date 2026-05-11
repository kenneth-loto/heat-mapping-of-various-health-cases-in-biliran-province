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
<link rel="stylesheet" href="../assets/css/table.css">
<link rel="stylesheet" href="../assets/css/requests.css">

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4 text-dark">Diseases</h5>
            <a class="btn btn-secondary fw-bold mb-3" role="button" href="add_resident.php">Add Disease</a>
            
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
                                    <th class="border-bottom-0 header text-light">Name of Disease</th>
                                    <th class="border-bottom-0 header text-light">Short Description</th>
                                    <th class="border-bottom-0 header text-light">Category</th>
                                    <th class="border-bottom-0 header text-light">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <?php
                                $i = 0; // Initialize counter for ID
                                $diseases = $function->getAllDiseases(); // Fetch disease data

                                if ($diseases) {
                                    foreach ($diseases as $disease) :
                                        $i++;
                                        $id = $disease['id'];
                                        $name_of_disease = $disease['name_of_disease'];
                                        $short_description = $disease['short_description'];
                                        $disease_category = $disease['disease_category'];
                                ?>
                                        <tr class="text-center">
                                            <td class="text-center"><?= $i; ?></td>
                                            <td><?= $name_of_disease; ?></td>
                                            <td><?= $short_description; ?></td>
                                            <td><?= $disease_category; ?></td>
                                            <td>
                                                <div  class="d-flex">
                                                    <a class="btn btn-warning me-2" href="update_disease.php?id=<?= $id; ?>" title="Edit Disease">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action='../navigate.php' method='post' style='display: inline;'>
                                                        <input type='hidden' name='id' value='<?= $disease['id']; ?>'>
                                                        <button class='btn btn-danger' name='btn-delete-disease' type='submit' onclick="return confirm('Are you sure you want to delete this disease?');" title='Delete Request'>
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
