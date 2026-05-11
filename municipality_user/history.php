<?php
include 'header.php';

$functions = new Functions();

// Fetch the approved requests only once
$requestsHistory = $functions->fetchAdminApprovedRequests();
?>

<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap5.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script defer src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
<link rel="stylesheet" href="../assets/css/requests.css">
<link rel="stylesheet" href="../assets/css/table.css">


<div class="container-fluid text-dark">
    <div class="card">
        <div class="card-body">
            <h3 class="mb-4 mt-2">Approved Requests History</h3>
            <table class="table table-bordered table-hover" id="table">
                <thead>
                    <tr>
                        <th class="header text-light">ID</th>
                        <th class="header text-light">Approved At</th>
                        <th class="header text-light">TD Number</th>
                        <th class="header text-light">PIN</th>
                        <th class="header text-light">Area (sq)</th>
                        <th class="header text-light">Tax Due</th>
                    </tr>
                </thead>
                <tbody class="fw-bold">
                    <?php
                    $i = 0; // Counter for formal ID starting from 1
                    if (!empty($requestsHistory)) {
                        foreach ($requestsHistory as $row) :
                            $i++; // Increment the counter for each transaction
                            $id = $row['id']; // Original ID for operations like Edit/Delete
                            $approved_at = $row['approved_at'];
                            $td_number = $row['td_number'];
                            $pin = $row['pin'];
                            $area = $row['area'];
                            $tax_due = $row['tax_due'];
                    ?>
                            <tr class="text-center text-dark">
                                <td style="text-align: center !important"><?= $i; ?></td> <!-- Display formal ID -->
                                <td>
                                    <?= date('F j, Y, g:i a', strtotime($approved_at)); ?>
                                </td>
                                <td>
                                    <label><?= htmlspecialchars($td_number); ?></label>
                                </td>
                                <td>
                                    <label><?= htmlspecialchars($pin); ?></label>
                                </td>
                                <td style="text-align: center !important">
                                    <label><?= htmlspecialchars($area); ?></label>
                                </td>
                                <td style="text-align: center !important">
                                    <label><?= htmlspecialchars($tax_due); ?></label>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    } else {
                        // Handle case where no transactions exist
                        echo "<tr><td colspan='6' class='text-center text-dark'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../assets/js/sidebarmenu.js"></script>

<script>
    $(document).ready(function () {
        $('#table').DataTable();
    });
</script>

</body>
</html>
