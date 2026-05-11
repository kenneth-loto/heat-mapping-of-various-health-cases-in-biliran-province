<?php

include_once '../session.php';
Session::init();
include '../function.php';
$function = new Functions();

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RPTMTCS</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
</head>
<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
         data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="../assets/images/logos/logo.png" width="200" alt="">
                                </a>
                                <h5 class="text-center mb-4"><strong>RPTMTCS</strong></h5>

                                <?php
                                    // Display session message if set
                                    $msg = Session::get("msg");
                                    if (isset($msg)) {
                                        echo $msg;
                                        Session::set("msg", NULL); // Clear the message after displaying it
                                    }
                                ?>

                                <form method="post" action="../navigate.php">
                                    <div class="mb-4">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" placeholder="e.g. example@gmail.com" required>
                                    </div>
                                    <button name="btn-password-reset" type="submit" class="btn btn-dark w-100 py-2">Send Reset Link</button>
                                </form>
                            </div>
                            <a class="text-primary fw-bold ms-2 d-flex align-items-center justify-content-center" href="applicant_login.php" style="margin-bottom: 20px">RETURN</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>