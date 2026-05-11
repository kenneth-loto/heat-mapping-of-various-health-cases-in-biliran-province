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
            <h5 class="card-title fw-semibold mb-4 text-dark">Municipality Users</h5>
            <a class="btn fw-bold mb-3 text-light" role="button" href="add_municipality_user.php" style="background-color: #006666">Add Municipality User</a>
            
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
                                    <th class="border-bottom-0 header text-light">Name</th>
                                    <th class="border-bottom-0 header text-light">Address</th>
                                    <th class="border-bottom-0 header text-light">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-dark">
                                <?php
                                $i = 0; // Initialize counter for ID
                                $municipality_users = $function->getAllMunicipalityUsers(); // Fetch disease data

                                if ($municipality_users) {
                                    foreach ($municipality_users as $municipality_user) :
                                        $i++;
                                        $id = $municipality_user->id;
                                        $first_name = $municipality_user->first_name;
                                        $middle_name = $municipality_user->middle_name;
                                        $last_name = $municipality_user->last_name;
                                        $suffix = $municipality_user->suffix;
                                        $province = $municipality_user->province;
                                        $municipality = $municipality_user->municipality;
                                        $barangay = $municipality_user->barangay;
                                        $street = $municipality_user->street;
                                ?>
                                        <tr class="text-align-left">
                                            <td class="text-center"><?= $i; ?></td>
                                            <td><?= $first_name . ' ' . $middle_name . ' ' . $last_name . ' ' . $suffix; ?></td>
                                            <td><?= $street . ' ' . $barangay . ' ' . $municipality . ', ' . $province; ?></td>
                                            <td>
                                                <div  class="d-flex justify-content-center">
                                                    <a class="btn btn-warning me-2" href="update_municipality_user.php?id=<?= $id; ?>" title="Update">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action='../navigate.php' method='post' style='display: inline;'>
                                                        <input type='hidden' name='id' value='<?= $id; ?>'>
                                                        <button class='btn btn-danger' name='btn-delete-municipality-user' type='submit' onclick="return confirm('Are you sure you want to delete this municipality user?');" title='Delete'>
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
